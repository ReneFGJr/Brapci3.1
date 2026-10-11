#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Task 0204: estatísticas da tabela brapci_elastic.dataset."""

import os
import sys
from pathlib import Path

import pymysql
from dotenv import load_dotenv
from rich import box
from rich.console import Console
from rich.panel import Panel
from rich.table import Table


console = Console()
BASE_DIR = Path(__file__).resolve().parent.parent
load_dotenv(BASE_DIR / ".env")

TASK = {
    "id": 204,
    "name": "Estatísticas do Dataset",
    "description": "Exibe estatísticas da tabela brapci_elastic.dataset.",
    "patterns": ["dataset statistics", "dataset estatisticas", "dataset estatísticas"],
    "parameters": [],
}


def get_connection():
    return pymysql.connect(
        host=os.getenv("DB_HOST", "localhost"),
        port=int(os.getenv("DB_PORT", 3306)),
        user=os.getenv("DB_USERNAME", "root"),
        password=os.getenv("DB_PASSWORD", ""),
        database="brapci_elastic",
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=True,
    )


def inteiro(valor):
    return int(valor or 0)


def percentual(valor, total):
    return (inteiro(valor) / inteiro(total) * 100) if inteiro(total) else 0.0


def coletar_estatisticas():
    conexao = get_connection()
    try:
        with conexao.cursor() as cursor:
            cursor.execute(
                """
                SELECT
                    COUNT(*) AS total,
                    SUM(status = 1) AS ativos,
                    SUM(status = 2) AS editoriais,
                    SUM(status NOT IN (1, 2) OR status IS NULL) AS outros_status,
                    SUM(`new` = 1) AS aguardando_indexacao,
                    SUM(`new` = 0) AS indexados,
                    COUNT(DISTINCT JOURNAL) AS revistas,
                    MIN(CASE WHEN YEAR BETWEEN 1801 AND YEAR(CURDATE()) THEN YEAR END) AS ano_inicial,
                    MAX(CASE WHEN YEAR BETWEEN 1801 AND YEAR(CURDATE()) THEN YEAR END) AS ano_final
                FROM dataset
                """
            )
            resumo = {chave: inteiro(valor) for chave, valor in (cursor.fetchone() or {}).items()}

            cursor.execute(
                """
                SELECT
                    COUNT(*) AS total_ativos,
                    SUM(TITLE IS NULL OR TRIM(TITLE) = '') AS sem_titulo,
                    SUM(AUTHORS IS NULL OR TRIM(AUTHORS) = '') AS sem_autores,
                    SUM(YEAR IS NULL OR YEAR = 0) AS sem_ano,
                    SUM(JOURNAL IS NULL OR JOURNAL = 0) AS sem_revista,
                    SUM(DOI IS NULL OR TRIM(DOI) = '') AS sem_doi,
                    SUM(ABSTRACT = 1 OR (ABSTRACTS IS NOT NULL AND TRIM(ABSTRACTS) != '')) AS com_resumo,
                    SUM(KEYWORD = 1 OR (KEYWORDS IS NOT NULL AND TRIM(KEYWORDS) != '')) AS com_keywords,
                    SUM(PDF = 1) AS com_pdf
                FROM dataset
                WHERE status = 1
                """
            )
            qualidade = {chave: inteiro(valor) for chave, valor in (cursor.fetchone() or {}).items()}
            qualidade["sem_resumo"] = qualidade["total_ativos"] - qualidade.pop("com_resumo", 0)
            qualidade["sem_keywords"] = qualidade["total_ativos"] - qualidade.pop("com_keywords", 0)

            cursor.execute(
                """
                SELECT COALESCE(NULLIF(TRIM(CLASS), ''), '(sem classe)') AS classe,
                       COUNT(*) AS total
                FROM dataset
                GROUP BY COALESCE(NULLIF(TRIM(CLASS), ''), '(sem classe)')
                ORDER BY total DESC
                """
            )
            classes = [
                {"classe": linha["classe"], "total": inteiro(linha["total"])}
                for linha in (cursor.fetchall() or [])
            ]

            cursor.execute(
                """
                SELECT JOURNAL AS id,
                       COALESCE(NULLIF(TRIM(PUBLICATION), ''), '(sem nome)') AS revista,
                       COUNT(*) AS total
                FROM dataset
                WHERE status = 1
                GROUP BY JOURNAL, PUBLICATION
                ORDER BY total DESC
                LIMIT 10
                """
            )
            revistas = [
                {"id": inteiro(linha["id"]), "revista": linha["revista"], "total": inteiro(linha["total"])}
                for linha in (cursor.fetchall() or [])
            ]

            return {
                "resumo": resumo,
                "qualidade": qualidade,
                "classes": classes,
                "top_revistas": revistas,
            }
    finally:
        conexao.close()


def exibir(dados):
    resumo = dados["resumo"]
    qualidade = dados["qualidade"]
    total_ativos = qualidade["total_ativos"]

    console.print()
    console.print(
        Panel(
            "[bold bright_cyan]BRAPCI ELASTIC DATASET[/bold bright_cyan]\n"
            "[dim]Visão estatística dos registros armazenados[/dim]",
            title="[bold blue]TASK 0204[/bold blue]",
            border_style="bright_blue",
            box=box.DOUBLE,
            width=min(console.width, 79),
        )
    )

    geral = Table(title="Resumo geral", box=box.ROUNDED, border_style="blue")
    geral.add_column("Indicador", style="cyan")
    geral.add_column("Total", justify="right", style="bold white")
    for rotulo, chave in (
        ("Registros", "total"),
        ("Ativos", "ativos"),
        ("Editoriais", "editoriais"),
        ("Outros status", "outros_status"),
        ("Indexados", "indexados"),
        ("Aguardando indexação", "aguardando_indexacao"),
        ("Revistas distintas", "revistas"),
    ):
        geral.add_row(rotulo, f"{resumo[chave]:,}".replace(",", "."))
    geral.add_row("Período", f"{resumo['ano_inicial']}–{resumo['ano_final']}")
    console.print(geral)

    qualidade_tabela = Table(title="Qualidade dos registros ativos", box=box.ROUNDED, border_style="yellow")
    qualidade_tabela.add_column("Pendência", style="yellow")
    qualidade_tabela.add_column("Registros", justify="right")
    qualidade_tabela.add_column("%", justify="right", style="bold")
    for rotulo, chave in (
        ("Sem título", "sem_titulo"),
        ("Sem autores", "sem_autores"),
        ("Sem ano", "sem_ano"),
        ("Sem revista", "sem_revista"),
        ("Sem DOI", "sem_doi"),
        ("Sem resumo", "sem_resumo"),
        ("Sem palavras-chave", "sem_keywords"),
    ):
        valor = qualidade[chave]
        qualidade_tabela.add_row(rotulo, f"{valor:,}".replace(",", "."), f"{percentual(valor, total_ativos):.1f}%")
    qualidade_tabela.add_row("Com PDF", f"{qualidade['com_pdf']:,}".replace(",", "."), f"{percentual(qualidade['com_pdf'], total_ativos):.1f}%")
    console.print(qualidade_tabela)

    classes_tabela = Table(title="Distribuição por classe", box=box.ROUNDED, border_style="magenta")
    classes_tabela.add_column("Classe", style="magenta")
    classes_tabela.add_column("Total", justify="right")
    for item in dados["classes"]:
        classes_tabela.add_row(str(item["classe"]), f"{item['total']:,}".replace(",", "."))
    console.print(classes_tabela)

    revistas_tabela = Table(title="10 revistas com mais registros ativos", box=box.ROUNDED, border_style="green")
    revistas_tabela.add_column("ID", justify="right", style="dim")
    revistas_tabela.add_column("Revista", style="green", overflow="fold")
    revistas_tabela.add_column("Total", justify="right", style="bold")
    for item in dados["top_revistas"]:
        revistas_tabela.add_row(str(item["id"]), str(item["revista"]), f"{item['total']:,}".replace(",", "."))
    console.print(revistas_tabela)
    console.print()


def run(parametros=None, chat=None, silent=False):
    try:
        dados = coletar_estatisticas()
    except Exception as erro:
        if not silent:
            console.print(f"[bold red]Erro ao consultar brapci_elastic.dataset:[/bold red] {erro}")
            return None
        return {"success": False, "error": str(erro)}

    resultado = {"success": True, **dados}
    if silent:
        return resultado

    exibir(dados)
    return None


if __name__ == "__main__":
    run(parametros=sys.argv[1:])
