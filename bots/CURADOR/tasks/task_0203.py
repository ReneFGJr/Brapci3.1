#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
=========================================================
Task 0203: Remover Duplicatas no RDF
=========================================================
Para cada grupo de duplicatas:
1. Define o menor ID como preferencial (IDpref).
2. Para os outros IDs (IDdupl):
   - Atualiza brapci_rdf.rdf_concept: cc_use = IDpref para id_cc = IDdupl
   - Atualiza brapci_rdf.rdf_data: d_r1 = IDpref quando d_r1 = IDdupl
   - Atualiza brapci_rdf.rdf_data: d_r2 = IDpref quando d_r2 = IDdupl
Exibe tela com bordas de texto em (0,0), campos coloridos e
atualização a cada 2 segundos.
"""

import csv
import os
from pathlib import Path
import re
import sys
import time

from dotenv import load_dotenv
import pymysql
from rich.console import Console

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
    "id": 203,
    "name": "Remover Duplicatas",
    "description": (
        "Redireciona registros duplicados no RDF: define o menor ID como preferencial (IDpref) "
        "e atualiza cc_use em brapci_rdf.rdf_concept, além de d_r1 e d_r2 em brapci_rdf.rdf_data."
    ),
    "patterns": [
        "remover duplicatas",
        "remover duplicados",
        "remover duplicata",
        "remover_duplicatas",
        "merge duplicatas",
        "mesclar duplicatas",
        "deletar duplicadas",
        "deletar duplicados",
    ],
    "parameters": [
        {
            "name": "opcao_ou_ids",
            "type": "string",
            "required": False,
            "description": "IDs específicos (ex: 26986 241324), limite (ex: 50, todos) ou 'simular'/'dry-run'.",
        }
    ],
}

BASE_DIR = Path(__file__).resolve().parent.parent
if str(BASE_DIR) not in sys.path:
    sys.path.insert(0, str(BASE_DIR))

load_dotenv(BASE_DIR / ".env")

# Cores ANSI
C_RESET = "\033[0m"
C_BOLD = "\033[1m"
C_DIM = "\033[2m"
C_BLUE = "\033[1;34m"
C_CYAN = "\033[1;36m"
C_GREEN = "\033[1;32m"
C_YELLOW = "\033[1;33m"
C_RED = "\033[1;31m"
C_MAGENTA = "\033[1;35m"
C_WHITE = "\033[1;37m"
C_GRAY = "\033[90m"


def erro(mensagem):
    return {
        "success": False,
        "error": mensagem,
    }


def get_connection(database="brapci_rdf"):
    """
    Retorna conexão com MySQL com transação manual (autocommit=False).
    """
    return pymysql.connect(
        host=os.getenv("DB_HOST", "localhost"),
        port=int(os.getenv("DB_PORT", 3306)),
        user=os.getenv("DB_USERNAME", "root"),
        password=os.getenv("DB_PASSWORD"),
        database=database,
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=False,
    )


def obter_grupos_duplicados(caminho_customizado=None, silent=False):
    """
    Obtém os grupos de duplicatas a partir do módulo task_0202.
    """
    try:
        try:
            from tasks import task_0202
        except ImportError:
            import task_0202

        registros, origem = task_0202.carregar_dataset(
            caminho_customizado=caminho_customizado,
            silent=True
        )
        if not registros:
            return []
        registros_ordenados = task_0202.ordenar_dataset(registros)
        return task_0202.encontrar_duplicatas(registros_ordenados)
    except Exception as e:
        if not silent:
            console.print(f"[bold red]Erro ao identificar duplicatas:[/bold red] {e}")
        return []


def aplicar_redirecionamento_rdf(cursor, id_pref, id_dupl):
    """
    Executa os updates para um par (id_pref, id_dupl):
    1. brapci_rdf.rdf_concept: cc_use = id_pref WHERE id_cc = id_dupl
    2. brapci_rdf.rdf_data: d_r1 = id_pref WHERE d_r1 = id_dupl
    3. brapci_rdf.rdf_data: d_r2 = id_pref WHERE d_r2 = id_dupl
    """
    # 1. Atualizar cc_use em rdf_concept
    sql_concept = """
        UPDATE brapci_rdf.rdf_concept
        SET cc_use = %s
        WHERE id_cc = %s
    """
    cursor.execute(sql_concept, (id_pref, id_dupl))
    afetados_concept = cursor.rowcount

    # 2. Atualizar d_r1 em rdf_data
    sql_r1 = """
        UPDATE brapci_rdf.rdf_data
        SET d_r1 = %s
        WHERE d_r1 = %s
    """
    cursor.execute(sql_r1, (id_pref, id_dupl))
    afetados_r1 = cursor.rowcount

    # 3. Atualizar d_r2 em rdf_data
    sql_r2 = """
        UPDATE brapci_rdf.rdf_data
        SET d_r2 = %s
        WHERE d_r2 = %s
    """
    cursor.execute(sql_r2, (id_pref, id_dupl))
    afetados_r2 = cursor.rowcount

    return {
        "id_pref": id_pref,
        "id_dupl": id_dupl,
        "concept_cc_use": afetados_concept,
        "data_d_r1": afetados_r1,
        "data_d_r2": afetados_r2,
    }


def len_visivel(texto):
    """
    Retorna a largura visual do texto, desconsiderando sequências de escape ANSI.
    """
    limpo = re.sub(r"\033\[[0-9;]*[a-zA-Z]", "", texto)
    return len(limpo)


def truncar_visivel(texto, max_len):
    """
    Trunca o texto para caber no limite visual max_len.
    """
    limpo = re.sub(r"\033\[[0-9;]*[a-zA-Z]", "", texto)
    if len(limpo) <= max_len:
        return texto
    return limpo[: max_len - 3] + "..."


def linha_borda(conteudo, largura=76):
    """
    Gera uma linha envolvida pelas bordas laterais ║ ... ║ com alinhamento preciso.
    """
    tam = len_visivel(conteudo)
    if tam > largura:
        conteudo = truncar_visivel(conteudo, largura)
        tam = len_visivel(conteudo)
    espacos = " " * max(0, largura - tam)
    return f"{C_BLUE}║{C_RESET} {conteudo}{espacos} {C_BLUE}║{C_RESET}"


def desenhar_tela_atualizacao(
    grupo_atual,
    total_grupos,
    id_pref,
    ids_dupl,
    titulo,
    revista,
    autores,
    concept_afetados,
    r1_afetados,
    r2_afetados,
    total_concept,
    total_r1,
    total_r2,
    historico,
    simular=False,
    tempo_inicio=None,
    largura=76,
    revista_titulo=None,
    ano=None,
):
    """
    Renderiza na tela (iniciando em linha 0 e coluna 0) um painel com bordas de texto,
    campos coloridos e estatísticas em tempo real da atualização corrente.
    """
    LARGURA_INTERNA = largura
    LARGURA_TOTAL = LARGURA_INTERNA + 2

    # Posiciona cursor na Linha 0, Coluna 0 (ANSI Home)
    sys.stdout.write("\033[H")

    # Linhas de borda horizontal
    borda_topo = f"{C_BLUE}╔{'═' * LARGURA_TOTAL}╗{C_RESET}"
    borda_divisoria = f"{C_BLUE}╠{'═' * LARGURA_TOTAL}╣{C_RESET}"
    borda_rodape = f"{C_BLUE}╚{'═' * LARGURA_TOTAL}╝{C_RESET}"

    # Informações de status e tempo
    modo_texto = f"{C_YELLOW}SIMULAÇÃO (DRY-RUN){C_RESET}" if simular else f"{C_GREEN}EXECUÇÃO REAL{C_RESET}"
    tempo_str = "00:00"
    if tempo_inicio:
        delta = int(time.time() - tempo_inicio)
        minutos, segundos = divmod(delta, 60)
        tempo_str = f"{minutos:02d}:{segundos:02d}"

    pct = (grupo_atual / total_grupos * 100) if total_grupos > 0 else 0.0

    # Truncamento de campos para ajuste visual
    tit_str = truncar_visivel(titulo or "(Sem título)", 56)
    aut_str = truncar_visivel(autores or "(Sem autores)", 56)
    rev_str = str(revista or "-")
    rev_nome = truncar_visivel(revista_titulo or "-", 32)
    ano_str = str(ano or "-")
    dupl_str = ", ".join(map(str, ids_dupl))
    if len(dupl_str) > 45:
        dupl_str = dupl_str[:42] + "..."

    linhas = []
    linhas.append(borda_topo)
    linhas.append(
        linha_borda(
            f"{C_CYAN}{C_BOLD}CURADOR  ::  TASK 0203 - REMOÇÃO DE DUPLICATAS NO RDF (brapci_rdf){C_RESET}",
            LARGURA_INTERNA,
        )
    )
    linhas.append(borda_divisoria)

    # Painel de Status
    linhas.append(
        linha_borda(
            f"Modo: {modo_texto}   {C_BLUE}│{C_RESET}   Status: {C_GREEN}ATUALIZANDO...{C_RESET}   {C_BLUE}│{C_RESET}   Intervalo: {C_WHITE}2.0s{C_RESET}",
            LARGURA_INTERNA,
        )
    )
    linhas.append(
        linha_borda(
            f"Progresso: {C_BOLD}{C_WHITE}[ {grupo_atual:,} / {total_grupos:,} ]{C_RESET} ({pct:.1f}%)   {C_BLUE}│{C_RESET}   Tempo: {C_YELLOW}{tempo_str}{C_RESET}",
            LARGURA_INTERNA,
        )
    )
    linhas.append(borda_divisoria)

    # Registro em Atualização
    linhas.append(
        linha_borda(
            f"{C_BOLD}{C_WHITE}REGISTRO EM ATUALIZAÇÃO AGORA:{C_RESET}",
            LARGURA_INTERNA,
        )
    )
    linhas.append(
        linha_borda(
            f"  {C_GRAY}•{C_RESET} {C_WHITE}Título       :{C_RESET} {C_WHITE}{C_BOLD}{tit_str}{C_RESET}",
            LARGURA_INTERNA,
        )
    )
    linhas.append(
        linha_borda(
            f"  {C_GRAY}•{C_RESET} {C_WHITE}Revista      :{C_RESET} {C_MAGENTA}{rev_nome}{C_RESET} {C_GRAY}(ID {rev_str}){C_RESET}   {C_BLUE}│{C_RESET}   {C_WHITE}Ano:{C_RESET} {C_YELLOW}{ano_str}{C_RESET}",
            LARGURA_INTERNA,
        )
    )
    linhas.append(
        linha_borda(
            f"  {C_GRAY}•{C_RESET} {C_WHITE}Autores      :{C_RESET} {C_CYAN}{aut_str}{C_RESET}",
            LARGURA_INTERNA,
        )
    )
    linhas.append(
        linha_borda(
            f"  {C_GRAY}•{C_RESET} {C_CYAN}IDpref (Menor):{C_RESET} {C_GREEN}{C_BOLD}{id_pref}{C_RESET}  {C_GREEN}[PREFERENCIAL / PRESERVADO]{C_RESET}",
            LARGURA_INTERNA,
        )
    )
    linhas.append(
        linha_borda(
            f"  {C_GRAY}•{C_RESET} {C_YELLOW}IDdupl (Outros):{C_RESET} {C_RED}{C_BOLD}{dupl_str}{C_RESET}  {C_YELLOW}[REDIRECIONADO]{C_RESET}",
            LARGURA_INTERNA,
        )
    )
    linhas.append(borda_divisoria)

    # Operações SQL executadas neste registro
    linhas.append(
        linha_borda(
            f"{C_BOLD}{C_WHITE}AÇÕES EXECUTADAS NO BANCO (brapci_rdf):{C_RESET}",
            LARGURA_INTERNA,
        )
    )
    linhas.append(
        linha_borda(
            f"  {C_GREEN}✓{C_RESET} {C_CYAN}rdf_concept{C_RESET} : cc_use = {C_GREEN}{id_pref}{C_RESET} (onde id_cc IN {ids_dupl}) {C_BLUE}→{C_RESET} {C_GREEN}{concept_afetados} linha(s){C_RESET}",
            LARGURA_INTERNA,
        )
    )
    linhas.append(
        linha_borda(
            f"  {C_GREEN}✓{C_RESET} {C_CYAN}rdf_data{C_RESET}    : d_r1 = {C_GREEN}{id_pref}{C_RESET}   (onde d_r1 IN {ids_dupl})   {C_BLUE}→{C_RESET} {C_YELLOW}{r1_afetados} linha(s){C_RESET}",
            LARGURA_INTERNA,
        )
    )
    linhas.append(
        linha_borda(
            f"  {C_GREEN}✓{C_RESET} {C_CYAN}rdf_data{C_RESET}    : d_r2 = {C_GREEN}{id_pref}{C_RESET}   (onde d_r2 IN {ids_dupl})   {C_BLUE}→{C_RESET} {C_YELLOW}{r2_afetados} linha(s){C_RESET}",
            LARGURA_INTERNA,
        )
    )
    linhas.append(borda_divisoria)

    # Estatísticas Acumuladas
    linhas.append(
        linha_borda(
            f"{C_BOLD}{C_WHITE}TOTAIS ACUMULADOS:{C_RESET}",
            LARGURA_INTERNA,
        )
    )
    linhas.append(
        linha_borda(
            f"  {C_GRAY}•{C_RESET} Grupos Processados: {C_GREEN}{grupo_atual:,}{C_RESET}   {C_BLUE}│{C_RESET}   rdf_concept (cc_use): {C_CYAN}{total_concept:,}{C_RESET} linhas",
            LARGURA_INTERNA,
        )
    )
    linhas.append(
        linha_borda(
            f"  {C_GRAY}•{C_RESET} rdf_data (d_r1)   : {C_YELLOW}{total_r1:,}{C_RESET} linhas {C_BLUE}│{C_RESET}   rdf_data (d_r2)     : {C_YELLOW}{total_r2:,}{C_RESET} linhas",
            LARGURA_INTERNA,
        )
    )
    linhas.append(borda_divisoria)

    # Histórico Recente
    linhas.append(
        linha_borda(
            f"{C_BOLD}{C_WHITE}HISTÓRICO RECENTE:{C_RESET}",
            LARGURA_INTERNA,
        )
    )
    ultimos = historico[-3:] if historico else []
    if not ultimos:
        linhas.append(
            linha_borda(f"  {C_GRAY}(Iniciando processamento...){C_RESET}", LARGURA_INTERNA)
        )
    else:
        for h in ultimos:
            h_tit = truncar_visivel(h.get("titulo", ""), 32)
            h_dupl = ", ".join(map(str, h.get("ids_dupl", [])))
            if len(h_dupl) > 14:
                h_dupl = h_dupl[:11] + "..."
            hist_linha = (
                f"  {C_GRAY}[{h['idx']:>3}]{C_RESET} {C_GREEN}{h['id_pref']}{C_RESET} "
                f"{C_YELLOW}←{C_RESET} {C_RED}{h_dupl}{C_RESET} {C_BLUE}│{C_RESET} {h_tit}"
            )
            linhas.append(linha_borda(hist_linha, LARGURA_INTERNA))

    linhas.append(borda_divisoria)
    linhas.append(
        linha_borda(
            f"{C_DIM}[Ctrl+C] Interromper com segurança  {C_BLUE}│{C_RESET}{C_DIM}  Atualização a cada 2 segundos{C_RESET}",
            LARGURA_INTERNA,
        )
    )
    linhas.append(borda_rodape)

    # Escreve todas as linhas da tela de uma vez só
    sys.stdout.write("\n".join(linhas) + "\n")
    sys.stdout.flush()


def exportar_log_csv(resultados, caminho_saida=None):
    """
    Exporta histórico das operações de remoção de duplicatas para CSV.
    """
    if caminho_saida is None:
        caminho_saida = BASE_DIR / "data" / "remocao_duplicatas.csv"
    else:
        caminho_saida = Path(caminho_saida)

    caminho_saida.parent.mkdir(parents=True, exist_ok=True)

    with open(caminho_saida, "w", encoding="utf-8-sig", newline="") as f:
        writer = csv.writer(f, delimiter=";")
        writer.writerow([
            "grupo",
            "id_pref",
            "id_dupl",
            "titulo",
            "concept_cc_use_afetados",
            "data_d_r1_afetados",
            "data_d_r2_afetados",
        ])
        for idx, res in enumerate(resultados, 1):
            writer.writerow([
                idx,
                res["id_pref"],
                ", ".join(map(str, res["ids_dupl"])),
                res.get("titulo", ""),
                res["concept_afetados"],
                res["r1_afetados"],
                res["r2_afetados"],
            ])

    return caminho_saida


def run(parametros=None, chat=None, silent=False):
    """
    Executa a remoção e redirecionamento de duplicatas com tela interativa.
    """
    parametros = parametros or []

    palavras_gatilho = {
        "remover",
        "duplicata",
        "duplicatas",
        "duplicado",
        "duplicados",
        "merge",
        "mesclar",
        "0203",
        "203",
    }
    params_reais = [p for p in parametros if str(p).lower().strip() not in palavras_gatilho]

    # Identificação de parâmetros
    simular = False
    limite = 50
    processar_todos = False
    ids_manuais = []
    caminho_customizado = None
    intervalo_segundos = 2000.0  # Conforme solicitado: atualização a cada 2 segundos

    for p in params_reais:
        p_str = str(p).strip().lower()

        if p_str in ("simular", "simulacao", "simulação", "dry-run", "teste"):
            simular = True
        elif p_str in ("todos", "all", "tudo", "completo", "-1"):
            processar_todos = True
            limite = None
        elif p_str in ("rapido", "fast", "--fast"):
            intervalo_segundos = 0.0
        elif p_str.isdigit():
            val = int(p_str)
            if val > 1000:
                ids_manuais.append(val)
            else:
                limite = val
        elif Path(p).exists() or p_str.endswith((".dataset", ".csv", ".json")):
            caminho_customizado = p

    # Se informado em milissegundos (ex: 2000ms), converte para segundos
    if intervalo_segundos > 60.0:
        intervalo_segundos = intervalo_segundos / 1000.0

    # Determina se deve exibir a tela interativa
    # Exibe a tela sempre que interativo/terminal, a menos que --silent ou --json seja passado
    params_lower = [str(x).lower() for x in parametros]
    mostrar_tela = (not silent) or (
        sys.stdout.isatty()
        and "--silent" not in params_lower
        and "--json" not in params_lower
    )

    grupos_para_processar = []

    if len(ids_manuais) >= 2:
        ids_ordenados = sorted(ids_manuais)
        id_pref = ids_ordenados[0]
        ids_dupl = ids_ordenados[1:]
        grupos_para_processar.append({
            "id_pref": id_pref,
            "ids_dupl": ids_dupl,
            "titulo": f"Manual ({len(ids_manuais)} IDs)",
            "revista": "-",
            "revista_titulo": "-",
            "ano": "-",
            "autores": "-",
        })
    else:
        if mostrar_tela:
            console.print("[cyan]Identificando registros duplicados no dataset...[/cyan]")

        duplicatas_encontradas = obter_grupos_duplicados(
            caminho_customizado=caminho_customizado,
            silent=not mostrar_tela
        )

        if not duplicatas_encontradas:
            if not mostrar_tela:
                return {
                    "success": True,
                    "total_grupos": 0,
                    "mensagem": "Nenhum registro duplicado encontrado para remoção.",
                }
            console.print("[bold green][OK] Nenhum registro duplicado encontrado para remoção.[/bold green]\n")
            return None

        for dup in duplicatas_encontradas:
            ids = sorted([int(i) for i in dup.get("ids", []) if i is not None])
            if len(ids) > 1:
                id_pref = ids[0]
                ids_dupl = ids[1:]
                grupos_para_processar.append({
                    "id_pref": id_pref,
                    "ids_dupl": ids_dupl,
                    "titulo": dup.get("titulo", "(Sem título)"),
                    "revista": dup.get("revista", "-"),
                    "revista_titulo": dup.get("revista_titulo", "-"),
                    "ano": dup.get("ano", "-"),
                    "autores": dup.get("autores", "-"),
                })

    if not processar_todos and limite is not None:
        itens_selecionados = grupos_para_processar[:limite]
    else:
        itens_selecionados = grupos_para_processar

    total_grupos = len(itens_selecionados)

    if total_grupos == 0:
        if not mostrar_tela:
            return {"success": True, "total_grupos": 0}
        console.print("[yellow]Nenhum grupo de duplicatas selecionado.[/yellow]\n")
        return None

    # Confirmação do usuário antes de realizar os UPDATEs no banco de dados
    total_duplicatas_a_processar = sum(len(g["ids_dupl"]) for g in itens_selecionados)

    if mostrar_tela and "-y" not in params_lower and "--yes" not in params_lower:
        console.print()
        console.rule("[bold cyan]Confirmação de Atualização no Banco de Dados[/bold cyan]")
        console.print(f"[bold white]Grupos de duplicatas selecionados :[/bold white] [bold green]{total_grupos:,}[/bold green]".replace(",", "."))
        console.print(f"[bold white]Total de IDs a redirecionar (IDdupl):[/bold white] [bold yellow]{total_duplicatas_a_processar:,}[/bold yellow]".replace(",", "."))
        modo_str = "[yellow]SIMULAÇÃO (sem gravar no banco)[/yellow]" if simular else "[bold red]EXECUÇÃO REAL (gravação no banco)[/bold red]"
        console.print(f"[bold white]Modo de operação                  :[/bold white] {modo_str}")
        console.print(f"[bold white]Tabelas afetadas                  :[/bold white] [cyan]brapci_rdf.rdf_concept (cc_use)[/cyan] e [cyan]brapci_rdf.rdf_data (d_r1, d_r2)[/cyan]")
        console.print()

        try:
            resposta = input("Pressione [ENTER] para confirmar e iniciar os UPDATEs (ou 'n' para cancelar): ").strip().lower()
            if resposta in ("n", "nao", "não", "no", "cancelar", "sair", "q", "quit"):
                console.print("\n[yellow]Operação cancelada. Nenhum UPDATE foi realizado no banco.[/yellow]\n")
                return {
                    "success": False,
                    "cancelado": True,
                    "mensagem": "Operação cancelada pelo usuário.",
                }
        except (KeyboardInterrupt, EOFError):
            console.print("\n[yellow]Operação cancelada. Nenhum UPDATE foi realizado no banco.[/yellow]\n")
            return {
                "success": False,
                "cancelado": True,
                "mensagem": "Operação cancelada pelo usuário.",
            }

    conn = None
    try:
        conn = get_connection("brapci_rdf")
        resultados = []
        historico = []

        total_concept = 0
        total_r1 = 0
        total_r2 = 0
        tempo_inicio = time.time()

        # Limpa tela uma vez antes de iniciar a exibição
        if mostrar_tela:
            os.system("cls" if os.name == "nt" else "clear")

        with conn.cursor() as cursor:
            for idx, g in enumerate(itens_selecionados, 1):
                id_pref = g["id_pref"]
                ids_dupl = g["ids_dupl"]

                concept_par = 0
                r1_par = 0
                r2_par = 0

                for id_dupl in ids_dupl:
                    res_par = aplicar_redirecionamento_rdf(cursor, id_pref, id_dupl)
                    concept_par += res_par["concept_cc_use"]
                    r1_par += res_par["data_d_r1"]
                    r2_par += res_par["data_d_r2"]

                total_concept += concept_par
                total_r1 += r1_par
                total_r2 += r2_par

                res_grupo = {
                    "idx": idx,
                    "id_pref": id_pref,
                    "ids_dupl": ids_dupl,
                    "titulo": g["titulo"],
                    "revista": g.get("revista", "-"),
                    "revista_titulo": g.get("revista_titulo", "-"),
                    "ano": g.get("ano", "-"),
                    "autores": g.get("autores", "-"),
                    "concept_afetados": concept_par,
                    "r1_afetados": r1_par,
                    "r2_afetados": r2_par,
                }
                resultados.append(res_grupo)
                historico.append(res_grupo)

                # Persiste a cada grupo se não for simulação
                if not simular:
                    conn.commit()

                # Atualiza a tela a partir de (linha 0, coluna 0)
                if mostrar_tela:
                    desenhar_tela_atualizacao(
                        grupo_atual=idx,
                        total_grupos=total_grupos,
                        id_pref=id_pref,
                        ids_dupl=ids_dupl,
                        titulo=g["titulo"],
                        revista=g.get("revista", "-"),
                        autores=g.get("autores", "-"),
                        concept_afetados=concept_par,
                        r1_afetados=r1_par,
                        r2_afetados=r2_par,
                        total_concept=total_concept,
                        total_r1=total_r1,
                        total_r2=total_r2,
                        historico=historico,
                        simular=simular,
                        tempo_inicio=tempo_inicio,
                        revista_titulo=g.get("revista_titulo", "-"),
                        ano=g.get("ano", "-"),
                    )

                # Intervalo de 2 segundos a cada atualização
                if idx < total_grupos and intervalo_segundos > 0:
                    time.sleep(intervalo_segundos)

        if simular:
            conn.rollback()

        # Salva arquivo de log em CSV
        caminho_csv = exportar_log_csv(resultados)

        if mostrar_tela:
            console.print()
            if simular:
                console.print("[bold yellow][!] Modo de simulação finalizado: nenhuma alteração gravada.[/bold yellow]")
            else:
                console.print(f"[bold green][OK] Processamento concluído! {len(resultados)} grupos atualizados.[/bold green]")
            console.print(f"[dim]Log completo das operações gravado em: {caminho_csv}[/dim]\n")
            return None

        return {
            "success": True,
            "simulacao": simular,
            "total_grupos": len(resultados),
            "total_duplicatas_redirecionadas": sum(len(r["ids_dupl"]) for r in resultados),
            "afetados_rdf_concept": total_concept,
            "afetados_rdf_data_r1": total_r1,
            "afetados_rdf_data_r2": total_r2,
            "arquivo_log": str(caminho_csv),
            "resultados": resultados[:50],
        }

    except KeyboardInterrupt:
        if conn:
            if not simular:
                conn.commit()
            else:
                conn.rollback()
        caminho_csv = exportar_log_csv(resultados)
        if mostrar_tela:
            console.print("\n[bold yellow][!] Execução interrompida pelo usuário (Ctrl+C).[/bold yellow]")
            console.print(f"[dim]Alterações até o grupo {len(resultados)} salvas no log: {caminho_csv}[/dim]\n")
            return None
        return {
            "success": True,
            "interrompido": True,
            "total_grupos": len(resultados),
            "arquivo_log": str(caminho_csv),
        }

    except Exception as e:
        if conn:
            conn.rollback()
        if mostrar_tela:
            console.print(f"\n[bold red]Erro ao remover duplicatas no RDF:[/bold red] {e}\n")
        return erro(str(e))

    finally:
        if conn:
            conn.close()


if __name__ == "__main__":
    run(parametros=sys.argv[1:], silent=False)
