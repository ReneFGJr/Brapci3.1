#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
=========================================================
Task 0300: Check Dataset (brapci_elastic.dataset)
=========================================================
Verifica as informações, integridade e qualidade do dataset
em brapci_elastic.dataset.
Contém a função principal remove_editorial() para limpeza de
editoriais e registros não-científicos (status = 2), utilizada
também pelo robô bots/ROBOTi/mod_elasticsearch.py.
"""

from decimal import Decimal
import json
import os
from pathlib import Path
import sys

from dotenv import load_dotenv
import pymysql
from rich.console import Console
from rich.panel import Panel
from rich.table import Table
from rich.text import Text

# Habilita suporte ANSI e UTF-8 no Windows
if os.name == "nt":
    os.system("")
if hasattr(sys.stdout, "reconfigure"):
    try:
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")
    except Exception:
        pass

console = Console()

TASK = {
    "id": 300,
    "name": "Check Dataset",
    "description": "Verifica informações de brapci_elastic.dataset e fornece a função principal remove_editorial.",
    "patterns": [
        "check dataset",
        "check all",
        "check_dataset",
        "check_all",
        "checar dataset",
        "verificar dataset",
    ],
    "parameters": [
        {
            "name": "opcao",
            "type": "string",
            "required": False,
            "description": "Opção adicional (ex: 'clean', 'amostra', 'erros', 'json').",
        }
    ],
}

BASE_DIR = Path(__file__).resolve().parent.parent
load_dotenv(BASE_DIR / ".env")


def erro(mensagem):
    return {
        "success": False,
        "error": mensagem,
    }


def to_int(val):
    if val is None:
        return 0
    if isinstance(val, (int, float, Decimal)):
        return int(val)
    try:
        return int(str(val).strip())
    except Exception:
        return 0


def get_connection(database="brapci_elastic"):
    """
    Retorna conexão com o MySQL usando configurações do .env do CURADOR.
    """
    return pymysql.connect(
        host=os.getenv("DB_HOST", "localhost"),
        port=int(os.getenv("DB_PORT", 3306)),
        user=os.getenv("DB_USERNAME", "root"),
        password=os.getenv("DB_PASSWORD", ""),
        database=database,
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=True,
    )


def remove_editorial(silent=False):
    """
    Função principal de remoção de editoriais e registros não-científicos:
    Varre a base brapci_elastic.dataset e atualiza para status = 2
    registros cujo título corresponde a editoriais, expedientes,
    normas de publicação e outras seções não-científicas.

    Esta função é utilizada tanto pela task_0300 quanto pelo módulo
    bots/ROBOTi/mod_elasticsearch.py.
    """
    if not silent:
        print("183 - Removendo editoriais")

    lt = [
        "Editorial",
        "Política editorial",
        "Editorial %",
        "Processo Editorial%",
        "Normas para publicação",
        "Expediente",
        "Expediente %",
        "EDITORIAL, %",
        "Normas de Publicação",
        "Apresentação %",
        "Revista B%",
        "(Sem título)",
    ]

    total_afetados = 0
    conn = get_connection("brapci_elastic")
    try:
        with conn.cursor() as cursor:
            for q in lt:
                if "%" in q:
                    qr = "UPDATE dataset SET status = 2 WHERE TITLE LIKE %s"
                else:
                    qr = "UPDATE dataset SET status = 2 WHERE TITLE = %s"
                afetados = cursor.execute(qr, (q,))
                total_afetados += (afetados or 0)
        return total_afetados
    finally:
        conn.close()


def coletar_estatisticas_dataset():
    """
    Realiza diagnósticos e agregações em brapci_elastic.dataset.
    """
    conn = get_connection("brapci_elastic")
    try:
        with conn.cursor() as cursor:
            # 1. Resumo Geral de Status e Indexação
            sql_geral = """
                SELECT 
                    COUNT(*) AS total,
                    SUM(status = 1) AS ativos,
                    SUM(status = 2) AS editoriais,
                    SUM(status NOT IN (1, 2) OR status IS NULL) AS outros_status,
                    SUM(new = 1) AS novos_para_indexar,
                    SUM(new = 0) AS ja_indexados,
                    SUM(new NOT IN (0, 1) OR new IS NULL) AS outros_new,
                    COUNT(DISTINCT JOURNAL) AS total_revistas,
                    MIN(CASE WHEN YEAR > 1800 AND YEAR <= 2030 THEN YEAR END) AS ano_min,
                    MAX(CASE WHEN YEAR > 1800 AND YEAR <= 2030 THEN YEAR END) AS ano_max
                FROM dataset
            """
            cursor.execute(sql_geral)
            geral_raw = cursor.fetchone() or {}

            geral = {k: to_int(v) for k, v in geral_raw.items()}

            # 2. Qualidade dos Metadados em Registros Ativos (status = 1)
            sql_qualidade = """
                SELECT 
                    COUNT(*) AS total_ativos,
                    SUM(TITLE IS NOT NULL AND TRIM(TITLE) != '') AS com_titulo,
                    SUM(TITLE IS NULL OR TRIM(TITLE) = '') AS sem_titulo,
                    SUM(AUTHORS IS NOT NULL AND TRIM(AUTHORS) != '') AS com_autores,
                    SUM(AUTHORS IS NULL OR TRIM(AUTHORS) = '') AS sem_autores,
                    SUM(YEAR > 0) AS com_ano,
                    SUM(YEAR IS NULL OR YEAR = 0) AS sem_ano,
                    SUM(JOURNAL > 0 AND PUBLICATION IS NOT NULL AND TRIM(PUBLICATION) != '') AS com_revista,
                    SUM(JOURNAL IS NULL OR JOURNAL = 0) AS sem_journal_id,
                    SUM(PUBLICATION IS NULL OR TRIM(PUBLICATION) = '') AS sem_publication_nome,
                    SUM(DOI IS NOT NULL AND TRIM(DOI) != '') AS com_doi,
                    SUM(ABSTRACT = 1 OR (ABSTRACTS IS NOT NULL AND TRIM(ABSTRACTS) != '')) AS com_resumo,
                    SUM(KEYWORD = 1 OR (KEYWORDS IS NOT NULL AND TRIM(KEYWORDS) != '')) AS com_keywords,
                    SUM(PDF = 1) AS com_pdf
                FROM dataset
                WHERE status = 1
            """
            cursor.execute(sql_qualidade)
            qualidade_raw = cursor.fetchone() or {}
            qualidade = {k: to_int(v) for k, v in qualidade_raw.items()}

            # 3. Top 5 Revistas com mais artigos ativos
            sql_top_revistas = """
                SELECT 
                    JOURNAL AS journal_id,
                    COALESCE(PUBLICATION, '(Sem nome de revista)') AS revista,
                    COUNT(*) AS total_artigos
                FROM dataset
                WHERE status = 1
                GROUP BY JOURNAL, PUBLICATION
                ORDER BY total_artigos DESC
                LIMIT 5
            """
            cursor.execute(sql_top_revistas)
            top_revistas = cursor.fetchall() or []

            # 4. Amostras de Inconsistências
            # 4a. Sem Autores
            sql_amostra_autores = """
                SELECT ID, TITLE, PUBLICATION, YEAR
                FROM dataset
                WHERE status = 1 AND (AUTHORS IS NULL OR TRIM(AUTHORS) = '')
                LIMIT 5
            """
            cursor.execute(sql_amostra_autores)
            amostra_sem_autores = cursor.fetchall() or []

            # 4b. Sem Journal ID
            sql_amostra_journal = """
                SELECT ID, TITLE, PUBLICATION, YEAR
                FROM dataset
                WHERE status = 1 AND (JOURNAL IS NULL OR JOURNAL = 0)
                LIMIT 5
            """
            cursor.execute(sql_amostra_journal)
            amostra_sem_journal = cursor.fetchall() or []

            return {
                "geral": geral,
                "qualidade": qualidade,
                "top_revistas": top_revistas,
                "amostras": {
                    "sem_autores": amostra_sem_autores,
                    "sem_journal": amostra_sem_journal,
                },
            }
    finally:
        conn.close()


def barra_percentual(pct, largura=15):
    """
    Gera uma representação textual de barra de progresso.
    """
    blocos = int(round((pct / 100.0) * largura))
    blocos = max(0, min(largura, blocos))
    return "█" * blocos + "░" * (largura - blocos)


def renderizar_painel_visual(estatisticas, status_editorial, erro_editorial=None, exibir_amostras=False):
    """
    Renderiza na tela um dashboard completo de diagnóstico do dataset.
    """
    geral = estatisticas["geral"]
    qual = estatisticas["qualidade"]
    top_rev = estatisticas["top_revistas"]
    amostras = estatisticas["amostras"]

    total_base = geral.get("total", 0)
    ativos = geral.get("ativos", 0)
    editoriais = geral.get("editoriais", 0)
    novos = geral.get("novos_para_indexar", 0)
    indexados = geral.get("ja_indexados", 0)

    pct_ativos = (ativos / total_base * 100) if total_base > 0 else 0
    pct_editoriais = (editoriais / total_base * 100) if total_base > 0 else 0
    pct_novos = (novos / total_base * 100) if total_base > 0 else 0
    pct_indexados = (indexados / total_base * 100) if total_base > 0 else 0

    console.print()
    console.rule("[bold cyan]CURADOR :: TASK 0300 - CHECK DATASET (brapci_elastic.dataset)[/bold cyan]")
    console.print()

    # Box 1: Status de Execução de remove_editorial
    if status_editorial:
        ed_msg = (
            f"[bold green][OK] Função principal remove_editorial() executada com sucesso.[/bold green]\n"
            f"[dim]Varredura de editoriais, expedientes e normas concluída. "
            f"Registros inativos/editoriais com status = 2: [bold yellow]{editoriais:,}[/bold yellow].[/dim]"
        ).replace(",", ".")
    else:
        ed_msg = f"[bold red][FALHA] Erro ao executar remove_editorial():[/bold red] {erro_editorial}"

    console.print(Panel(ed_msg, title="[bold white]Módulo Editorial (remove_editorial)[/bold white]", border_style="cyan"))
    console.print()

    # Tabela 1: Resumo do Dataset
    tab_geral = Table(
        title="1. Resumo do Dataset e Indexação",
        header_style="bold white on blue",
        show_lines=True,
    )
    tab_geral.add_column("Métrica", style="bold white", width=34)
    tab_geral.add_column("Quantidade", justify="right", style="cyan", width=14)
    tab_geral.add_column("% do Total", justify="right", style="yellow", width=12)
    tab_geral.add_column("Distribuição Visual", justify="center", width=20)
    tab_geral.add_column("Significado", style="dim white")

    tab_geral.add_row(
        "Total Geral de Registros",
        f"{total_base:,}".replace(",", "."),
        "100.0%",
        f"[cyan]{barra_percentual(100.0)}[/cyan]",
        "Base total em brapci_elastic.dataset",
    )
    tab_geral.add_row(
        "Artigos Ativos (status = 1)",
        f"{ativos:,}".replace(",", "."),
        f"{pct_ativos:.1f}%",
        f"[green]{barra_percentual(pct_ativos)}[/green]",
        "Disponíveis para consulta e curadoria",
    )
    tab_geral.add_row(
        "Editoriais / Inativos (status = 2)",
        f"{editoriais:,}".replace(",", "."),
        f"{pct_editoriais:.1f}%",
        f"[yellow]{barra_percentual(pct_editoriais)}[/yellow]",
        "Removidos do índice científico ativo",
    )
    tab_geral.add_row(
        "Indexados no Elastic (new = 0)",
        f"{indexados:,}".replace(",", "."),
        f"{pct_indexados:.1f}%",
        f"[blue]{barra_percentual(pct_indexados)}[/blue]",
        "Sincronizados no índice Elasticsearch",
    )
    tab_geral.add_row(
        "Pendentes de Indexação (new = 1)",
        f"{novos:,}".replace(",", "."),
        f"{pct_novos:.1f}%",
        f"[magenta]{barra_percentual(pct_novos)}[/magenta]",
        "Aguardando exportação (task 2000)",
    )

    console.print(tab_geral)
    console.print()

    # Tabela 2: Qualidade dos Metadados nos Artigos Ativos
    total_atv = qual.get("total_ativos", 0)
    tab_qual = Table(
        title=f"2. Auditoria de Metadados nos Artigos Ativos (Total: {total_atv:,})".replace(",", "."),
        header_style="bold white on dark_green",
        show_lines=True,
    )
    tab_qual.add_column("Metadado / Campo", style="bold white", width=22)
    tab_qual.add_column("Preenchidos", justify="right", style="green", width=13)
    tab_qual.add_column("Vazios / Ausentes", justify="right", style="red", width=18)
    tab_qual.add_column("% Preenchimento", justify="right", width=16)
    tab_qual.add_column("Barra", justify="center", width=18)
    tab_qual.add_column("Status", justify="center", width=12)

    itens_qual = [
        ("Título (TITLE)", qual.get("com_titulo", 0), qual.get("sem_titulo", 0)),
        ("Autores (AUTHORS)", qual.get("com_autores", 0), qual.get("sem_autores", 0)),
        ("Ano (YEAR)", qual.get("com_ano", 0), qual.get("sem_ano", 0)),
        ("Revista Vinculada", qual.get("com_revista", 0), qual.get("sem_journal_id", 0)),
        ("Resumo (ABSTRACT)", qual.get("com_resumo", 0), total_atv - qual.get("com_resumo", 0)),
        ("Palavras-chave (KW)", qual.get("com_keywords", 0), total_atv - qual.get("com_keywords", 0)),
        ("Link / Arquivo PDF", qual.get("com_pdf", 0), total_atv - qual.get("com_pdf", 0)),
        ("Identificador DOI", qual.get("com_doi", 0), total_atv - qual.get("com_doi", 0)),
    ]

    for nome, ok_cnt, bad_cnt in itens_qual:
        pct = (ok_cnt / total_atv * 100) if total_atv > 0 else 0
        if pct >= 95.0:
            status_badge = "[bold green]EXCELENTE[/bold green]"
            cor_barra = "green"
        elif pct >= 70.0:
            status_badge = "[bold yellow]BOM[/bold yellow]"
            cor_barra = "yellow"
        elif pct >= 50.0:
            status_badge = "[bold red]REGULAR[/bold red]"
            cor_barra = "dark_orange"
        else:
            status_badge = "[bold red]BAIXO[/bold red]"
            cor_barra = "red"

        tab_qual.add_row(
            nome,
            f"{ok_cnt:,}".replace(",", "."),
            f"{bad_cnt:,}".replace(",", "."),
            f"{pct:.1f}%",
            f"[{cor_barra}]{barra_percentual(pct)}[/{cor_barra}]",
            status_badge,
        )

    console.print(tab_qual)
    console.print()

    # Tabela 3: Top 5 Revistas
    tab_rev = Table(
        title="3. Principais Revistas no Dataset Ativo",
        header_style="bold white on dark_blue",
        show_lines=True,
    )
    tab_rev.add_column("ID Jnl", justify="center", style="dim cyan", width=8)
    tab_rev.add_column("Título da Revista (PUBLICATION)", style="white")
    tab_rev.add_column("Artigos Ativos", justify="right", style="bold yellow", width=16)
    tab_rev.add_column("% do Ativo", justify="right", style="cyan", width=12)

    for r in top_rev:
        q = to_int(r.get("total_artigos", 0))
        pct_rev = (q / total_atv * 100) if total_atv > 0 else 0
        tab_rev.add_row(
            str(r.get("journal_id") or "-"),
            str(r.get("revista") or "-"),
            f"{q:,}".replace(",", "."),
            f"{pct_rev:.1f}%",
        )

    console.print(tab_rev)
    console.print()

    # Exibição de amostras de inconsistência se solicitado ou se houver problemas
    if exibir_amostras or "--amostra" in sys.argv:
        if amostras.get("sem_autores"):
            console.print("[bold yellow]Amostra de registros sem autores (AUTHORS vazio):[/bold yellow]")
            for a in amostras["sem_autores"]:
                console.print(f"  • ID {a.get('ID')}: {a.get('TITLE')} ({a.get('YEAR')}) - {a.get('PUBLICATION')}")
            console.print()

        if amostras.get("sem_journal"):
            console.print("[bold yellow]Amostra de registros sem ID de revista (JOURNAL = 0):[/bold yellow]")
            for a in amostras["sem_journal"]:
                console.print(f"  • ID {a.get('ID')}: {a.get('TITLE')} ({a.get('YEAR')}) - {a.get('PUBLICATION')}")
            console.print()

    # Dicas e Próximos Passos
    dicas = (
        "[bold cyan]Próximas ações recomendadas no CURADOR:[/bold cyan]\n"
        " • [bold white]checar duplicatas[/bold white] (task 0202) : Detecta artigos duplicados no dataset ativo (status = 1)\n"
        " • [bold white]remover duplicatas[/bold white] (task 0203) : Redireciona IDs duplicados no RDF com tela interativa\n"
        " • [bold white]elastic export[/bold white] (task 2000)     : Exporta os registros pendentes (new = 1) para o Elasticsearch"
    )
    console.print(Panel(dicas, title="[bold white]Recomendações do CURADOR[/bold white]", border_style="blue"))
    console.print()


def run(parametros=None, chat=None, silent=False):
    """
    Executa a verificação completa do dataset e a remoção de editoriais.
    """
    parametros = parametros or []

    # Filtrar palavras de ativação do comando
    palavras_gatilho = {
        "check",
        "dataset",
        "all",
        "check_dataset",
        "check_all",
        "checar",
        "verificar",
        "0300",
        "300",
    }
    params_reais = [p for p in parametros if str(p).lower().strip() not in palavras_gatilho]

    # Flags
    exibir_amostras = any(p.lower() in ("amostra", "amostras", "erros", "inconsistencias", "--amostra") for p in params_reais)
    apenas_clean = any(p.lower() in ("clean", "limpar", "editorial", "editoriais") for p in params_reais)

    try:
        if not silent:
            console.print("[cyan]Executando função principal [bold]remove_editorial()[/bold]...[/cyan]")

        # 1. Executa função principal remove_editorial da task_0300
        try:
            remove_editorial(silent=silent)
            ok_ed = True
            err_ed = None
        except Exception as e_ed:
            ok_ed = False
            err_ed = str(e_ed)

        # 2. Coleta dados e diagnósticos do dataset
        estatisticas = coletar_estatisticas_dataset()

        # Se for apenas para limpar editoriais
        if apenas_clean:
            if not silent:
                console.print(f"[bold green][OK] Editoriais verificados com sucesso. Total com status = 2: {estatisticas['geral']['editoriais']:,}[/bold green]\n".replace(",", "."))
                return None
            return {
                "success": True,
                "remove_editorial": ok_ed,
                "erro_editorial": err_ed,
                "editoriais": estatisticas["geral"]["editoriais"],
            }

        # 3. Exibição na tela
        if not silent:
            renderizar_painel_visual(
                estatisticas=estatisticas,
                status_editorial=ok_ed,
                erro_editorial=err_ed,
                exibir_amostras=exibir_amostras,
            )
            return None

        # Modo silencioso / API
        return {
            "success": True,
            "remove_editorial": ok_ed,
            "erro_editorial": err_ed,
            "geral": estatisticas["geral"],
            "qualidade": estatisticas["qualidade"],
            "top_revistas": estatisticas["top_revistas"],
        }

    except Exception as e:
        if not silent:
            console.print(f"[bold red]Erro ao verificar dataset:[/bold red] {e}")
        return erro(str(e))


if __name__ == "__main__":
    run(parametros=sys.argv[1:], silent=False)
