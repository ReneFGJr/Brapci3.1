#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
=========================================================
Task 0202: Checar Duplicatas no Dataset
=========================================================
Abre o arquivo/tabela brapci_elastic.dataset, ordena por título,
revista e autores, e verifica registros duplicados.
"""

import csv
from itertools import groupby
import json
import os
from pathlib import Path

from dotenv import load_dotenv
import pymysql
from rich.console import Console
from rich.table import Table

console = Console()

TASK = {
    "id": 202,
    "name": "Checar Duplicatas",
    "description": "Verifica registros duplicados no brapci_elastic.dataset por título, revista e autores.",
    "patterns": [
        "checar duplicatas",
        "checar duplicados",
        "checar",
        "duplicatas",
        "duplicados",
        "check duplicates",
        "checar_duplicatas",
    ],
    "parameters": [
        {
            "name": "opcao",
            "type": "string",
            "required": False,
            "description": "Limite de exibição (ex: 20, 50, todos), ação (export, csv, memoria) ou arquivo alternativo.",
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


def get_connection(database="brapci_elastic"):
    """
    Retorna conexão com o MySQL usando configurações do .env.
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


def tentar_ler_arquivo(caminho):
    """
    Tenta ler registros a partir de um arquivo (.dataset, .json, .jsonl, .csv).
    """
    arquivo = Path(caminho)
    if not arquivo.exists() or not arquivo.is_file():
        return None

    registros = []

    # 1. Tenta carregar como JSON ou JSON Lines
    try:
        with open(arquivo, "r", encoding="utf-8") as f:
            conteudo = f.read().strip()
            if conteudo.startswith("["):
                dados = json.loads(conteudo)
                if isinstance(dados, list):
                    return dados
            elif conteudo.startswith("{"):
                dados = json.loads(conteudo)
                if isinstance(dados, dict):
                    for chave in ("data", "records", "items", "dataset"):
                        if chave in dados and isinstance(dados[chave], list):
                            return dados[chave]
                    return [dados]

            # JSON Lines
            f.seek(0)
            for linha in f:
                linha = linha.strip()
                if linha:
                    try:
                        registros.append(json.loads(linha))
                    except json.JSONDecodeError:
                        break
            if registros:
                return registros
    except Exception:
        pass

    # 2. Tenta carregar como CSV / TSV
    try:
        with open(arquivo, "r", encoding="utf-8", errors="replace") as f:
            primeira_linha = f.readline()
            delimitador = ";" if ";" in primeira_linha else ("," if "," in primeira_linha else "\t")
            f.seek(0)
            reader = csv.DictReader(f, delimiter=delimitador)
            registros = list(reader)
            if registros:
                return registros
    except Exception:
        pass

    return None


def carregar_dataset(caminho_customizado=None, silent=False):
    """
    Carrega o dataset a partir de arquivo em disco ou diretamente da tabela brapci_elastic.dataset.
    """
    candidatos_arquivo = []
    if caminho_customizado:
        candidatos_arquivo.append(Path(caminho_customizado))

    # Caminhos padrão para procura do arquivo brapci_elastic.dataset
    candidatos_arquivo.extend([
        Path("brapci_elastic.dataset"),
        Path("data/brapci_elastic.dataset"),
        BASE_DIR / "brapci_elastic.dataset",
        BASE_DIR / "data" / "brapci_elastic.dataset",
        BASE_DIR.parent / "brapci_elastic.dataset",
    ])

    for cand in candidatos_arquivo:
        registros = tentar_ler_arquivo(cand)
        if registros:
            if not silent:
                console.print(f"[bold cyan]Arquivo carregado:[/bold cyan] {cand} ({len(registros)} registros)")
            return registros, f"Arquivo: {cand}"

    # Se não houver arquivo físico, lê da tabela MySQL brapci_elastic.dataset
    if not silent:
        console.print("[cyan]Carregando dados da tabela [bold]brapci_elastic.dataset[/bold] (MySQL)...[/cyan]")

    try:
        conn = get_connection("brapci_elastic")
        with conn.cursor() as cursor:
            sql = """
                SELECT id_ds, ID, TITLE, JOURNAL, PUBLICATION, AUTHORS, YEAR, DOI, updated_at
                FROM dataset
                ORDER BY TITLE, PUBLICATION, JOURNAL, YEAR, AUTHORS, ID
            """
            cursor.execute(sql)
            registros = cursor.fetchall()
            return registros, "MySQL: brapci_elastic.dataset"
    except pymysql.MySQLError as e:
        raise RuntimeError(f"Erro ao acessar banco brapci_elastic.dataset: {e}")


def chave_ordenacao(r):
    """
    Chave normalizada para ordenação e agrupamento por:
    - Título do artigo (TITLE)
    - Título da revista (PUBLICATION)
    - ID da revista (JOURNAL)
    - Ano de publicação (YEAR)
    - Autores (AUTHORS)
    """
    titulo = str(r.get("TITLE") or r.get("title") or "").strip().lower()
    revista_titulo = str(r.get("PUBLICATION") or r.get("publication") or "").strip().lower()
    revista_id = str(r.get("JOURNAL") or r.get("journal") or "").strip()
    ano = str(r.get("YEAR") or r.get("year") or "").strip()
    autores = str(r.get("AUTHORS") or r.get("authors") or r.get("autores") or "").strip().lower()
    return (titulo, revista_titulo, revista_id, ano, autores)


def ordenar_dataset(registros):
    """
    Ordena os registros por título, revista (título e ID), ano e autores.
    """
    registros.sort(key=chave_ordenacao)
    return registros


def encontrar_duplicatas(registros_ordenados):
    """
    Percorre os registros ordenados e agrupa os que têm mesmo título,
    título da revista, ID da revista, ano de publicação e autores.
    """
    duplicatas = []

    for chave, grupo_iter in groupby(registros_ordenados, key=chave_ordenacao):
        grupo = list(grupo_iter)
        if len(grupo) > 1:
            primeiro = grupo[0]
            titulo = primeiro.get("TITLE") or primeiro.get("title") or "(Sem título)"
            revista_id = str(primeiro.get("JOURNAL") or primeiro.get("journal") or "")
            revista_titulo = primeiro.get("PUBLICATION") or primeiro.get("publication") or ""
            ano = str(primeiro.get("YEAR") or primeiro.get("year") or "")
            autores = primeiro.get("AUTHORS") or primeiro.get("authors") or "(Sem autores)"

            ids = [
                r.get("ID") or r.get("id")
                for r in grupo
                if (r.get("ID") or r.get("id")) is not None
            ]
            anos = [
                str(r.get("YEAR") or r.get("year") or "")
                for r in grupo
                if r.get("YEAR") or r.get("year")
            ]

            duplicatas.append({
                "titulo": titulo,
                "revista": revista_id,
                "revista_titulo": revista_titulo,
                "ano": ano,
                "autores": autores,
                "quantidade": len(grupo),
                "ids": ids,
                "anos": list(dict.fromkeys(anos)),
                "registros": grupo,
            })

    return duplicatas


def exportar_csv(duplicatas, caminho_saida=None):
    """
    Exporta a lista de duplicatas para um arquivo CSV incluindo ano e título da revista.
    """
    if caminho_saida is None:
        caminho_saida = BASE_DIR / "data" / "duplicatas_brapci_elastic.csv"
    else:
        caminho_saida = Path(caminho_saida)

    caminho_saida.parent.mkdir(parents=True, exist_ok=True)

    with open(caminho_saida, "w", encoding="utf-8-sig", newline="") as f:
        writer = csv.writer(f, delimiter=";")
        writer.writerow(["grupo", "titulo", "revista_id", "revista_titulo", "ano", "autores", "quantidade", "ids_brapci"])
        for idx, dup in enumerate(duplicatas, 1):
            writer.writerow([
                idx,
                dup["titulo"],
                dup["revista"],
                dup["revista_titulo"],
                dup["ano"],
                dup["autores"],
                dup["quantidade"],
                ", ".join(map(str, dup["ids"])),
            ])

    return caminho_saida


def salvar_em_memoria(ids_duplicados):
    """
    Salva os IDs duplicados na memória temporária do CURADOR (task_4000).
    """
    try:
        from tasks import task_4000
        task_4000.IDs = ids_duplicados
        task_4000.salvar_ids()
        return True
    except Exception:
        return False


def exibir_tabela(duplicatas, total_registros, origem, limite=25):
    """
    Renderiza uma tabela Rich elegante com os registros duplicados.
    """
    total_grupos = len(duplicatas)
    total_registros_duplicados = sum(d["quantidade"] for d in duplicatas)

    console.print()
    console.rule("[bold cyan]CURADOR - Checagem de Duplicatas (brapci_elastic.dataset)[/bold cyan]")
    console.print(f"[bold white]Origem dos dados:[/bold white] [green]{origem}[/green]")
    console.print(f"[bold white]Total de registros no dataset:[/bold white] [cyan]{total_registros:,}[/cyan]".replace(",", "."))
    console.print(f"[bold white]Grupos com duplicidade:[/bold white] [bold red]{total_grupos:,}[/bold red]".replace(",", "."))
    console.print(f"[bold white]Registros duplicados:[/bold white] [bold yellow]{total_registros_duplicados:,}[/bold yellow]".replace(",", "."))
    console.print()

    if not duplicatas:
        console.print("[bold green]✓ Nenhum registro duplicado encontrado![/bold green]\n")
        return

    itens_exibir = duplicatas if limite is None else duplicatas[:limite]

    table = Table(
        title=f"Duplicatas Encontradas (Exibindo {len(itens_exibir)} de {total_grupos})",
        header_style="bold white on blue",
        show_lines=True,
    )

    table.add_column("#", style="yellow", justify="right", width=5)
    table.add_column("Título", style="white", min_width=30, max_width=45)
    table.add_column("Revista", style="cyan", justify="center", width=8)
    table.add_column("Autores", style="magenta", min_width=20, max_width=30)
    table.add_column("Qtd", style="bold red", justify="center", width=5)
    table.add_column("IDs BRAPCI", style="green", min_width=15)

    for i, item in enumerate(itens_exibir, 1):
        titulo = item["titulo"]
        if len(titulo) > 60:
            titulo = titulo[:57] + "..."

        autores = item["autores"]
        if len(autores) > 40:
            autores = autores[:37] + "..."

        ids_str = ", ".join(map(str, item["ids"][:10]))
        if len(item["ids"]) > 10:
            ids_str += f" (+{len(item['ids']) - 10})"

        table.add_row(
            str(i),
            titulo,
            str(item["revista"]),
            autores,
            str(item["quantidade"]),
            ids_str,
        )

    console.print(table)
    console.print()

    if limite is not None and total_grupos > limite:
        console.print(
            f"[dim]Exibindo {limite} de {total_grupos} grupos duplicados. "
            f"Use '[bold]checar duplicatas 50[/bold]' ou '[bold]checar duplicatas todos[/bold]' para ver mais.[/dim]"
        )
    console.print(
        "[dim]Dica: use '[bold]checar duplicatas export[/bold]' para gerar CSV completo ou "
        "'[bold]checar duplicatas memoria[/bold]' para guardar IDs no Robô.[/dim]\n"
    )


def run(parametros=None, chat=None, silent=False):
    """
    Executa a verificação de duplicatas no dataset.
    """
    parametros = parametros or []

    # Filtrar palavras de ativação do comando se passadas como parâmetros
    palavras_gatilho = {
        "checar",
        "duplicata",
        "duplicatas",
        "duplicado",
        "duplicados",
        "check",
        "duplicates",
        "0202",
        "202",
    }
    params_reais = [p for p in parametros if str(p).lower().strip() not in palavras_gatilho]

    # Interpretação dos parâmetros
    limite = 25
    exportar = False
    salvar_mem = False
    caminho_customizado = None

    for p in params_reais:
        p_str = str(p).strip().lower()

        if p_str in ("todos", "all", "tudo", "completo", "-1"):
            limite = None
        elif p_str.isdigit():
            limite = int(p_str)
        elif p_str in ("export", "exportar", "csv", "salvar"):
            exportar = True
        elif p_str in ("memoria", "memória", "memory", "set", "ids"):
            salvar_mem = True
        elif Path(p).exists() or p_str.endswith((".dataset", ".csv", ".json", ".txt")):
            caminho_customizado = p

    try:
        # 1. Abre o arquivo ou tabela brapci_elastic.dataset
        registros, origem = carregar_dataset(caminho_customizado=caminho_customizado, silent=silent)

        if not registros:
            if silent:
                return erro("Nenhum registro encontrado no dataset.")
            console.print("[bold red]Nenhum registro encontrado no dataset.[/bold red]")
            return erro("Nenhum registro encontrado no dataset.")

        total_registros = len(registros)

        # 2. Ordena por titulo, revista e autores
        registros_ordenados = ordenar_dataset(registros)

        # 3. Checa registros duplicados
        duplicatas = encontrar_duplicatas(registros_ordenados)

        # Exportação se solicitada
        caminho_csv = None
        if exportar:
            caminho_csv = exportar_csv(duplicatas)
            if not silent:
                console.print(f"[bold green]✓ Relatório CSV salvo em:[/bold green] [cyan]{caminho_csv}[/cyan]")

        # Salvar IDs em memória se solicitado
        if salvar_mem:
            todos_ids = []
            for d in duplicatas:
                todos_ids.extend(d["ids"])
            salvou = salvar_em_memoria(todos_ids)
            if not silent:
                if salvou:
                    console.print(f"[bold green]✓ {len(todos_ids)} IDs duplicados armazenados na memória (task_4000).[/bold green]")
                else:
                    console.print("[yellow]Não foi possível salvar na memória temporária.[/yellow]")

        # Exibição visual
        if not silent:
            exibir_tabela(duplicatas, total_registros, origem, limite=limite)
            return None

        # Retorno estruturado para modo silencioso / CLI
        return {
            "success": True,
            "origem": origem,
            "total_registros": total_registros,
            "total_grupos_duplicados": len(duplicatas),
            "total_registros_duplicados": sum(d["quantidade"] for d in duplicatas),
            "csv_exportado": str(caminho_csv) if caminho_csv else None,
            "duplicatas": [
                {
                    "titulo": d["titulo"],
                    "revista": d["revista"],
                    "autores": d["autores"],
                    "quantidade": d["quantidade"],
                    "ids": d["ids"],
                    "anos": d["anos"],
                }
                for d in duplicatas[:limite if limite is not None else len(duplicatas)]
            ],
        }

    except Exception as e:
        if not silent:
            console.print(f"[bold red]Erro ao processar duplicatas:[/bold red] {e}")
        return erro(str(e))


if __name__ == "__main__":
    import sys
    run(parametros=sys.argv[1:], silent=False)
