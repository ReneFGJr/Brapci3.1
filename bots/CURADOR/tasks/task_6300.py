#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Task 6300
Sources
"""

import requests

from rich.console import Console
from rich.table import Table

console = Console()

TASK = {
    "id": 6300,
    "name": "SOURCES",
    "description": "Gerenciar fontes do sistema.",
    "patterns": ["sources", "source", "fontes", "fonte"],
    "parameters": [{
        "name": "nome",
        "type": "string",
        "required": True
    }]
}

import json
import requests
import pymysql
import sys

from pathlib import Path
import os

import pymysql
from dotenv import load_dotenv

# Localiza o .env na raiz do projeto
BASE_DIR = Path(__file__).resolve().parent.parent
load_dotenv(BASE_DIR / ".env")


def erro(mensagem):
    return {
        "success": False,
        "error": mensagem,
    }


def get_connection(database=None):
    """
    Retorna uma conexão MySQL utilizando as configurações do .env.

    Parameters
    ----------
    database : str | None
        Se informado, sobrescreve o banco definido no .env.
    """

    return pymysql.connect(
        host=os.getenv("DB_HOST", "localhost"),
        port=int(os.getenv("DB_PORT", 3306)),
        user=os.getenv("DB_USERNAME"),
        password=os.getenv("DB_PASSWORD"),
        database=database or os.getenv("DB_DATABASE"),
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=False,
    )


def source_list(silent=False):
    conn = None

    historic_labels = {
        "0": "Correntes",
        "1": "Historicas",
    }

    oai_labels = {
        "100": "Para coletar",
        "200": "Coletados",
        "404": "Site fora do ar",
        "500": "Erros de coleta",
        "501": "Erros de acesso",
    }

    try:
        conn = get_connection("brapci")

        with conn.cursor() as cur:
            cur.execute("SELECT * FROM source_source")
            rows = cur.fetchall()

        summary_oai = {}
        summary_historic = {}
        summary_combined = {}

        for row in rows:
            oai_status = str(row.get("jnl_oai_status", "")).strip() or "(vazio)"
            historic = str(row.get("jnl_historic", "")).strip() or "(vazio)"

            summary_oai[oai_status] = summary_oai.get(oai_status, 0) + 1
            summary_historic[historic] = summary_historic.get(historic, 0) + 1
            combo_key = (historic, oai_status)
            summary_combined[combo_key] = summary_combined.get(combo_key, 0) + 1

        enriched_combined = []
        for (historic, oai_status), total in sorted(
            summary_combined.items(), key=lambda x: (-x[1], x[0][0], x[0][1])
        ):
            enriched_combined.append(
                {
                    "jnl_historic": historic,
                    "jnl_historic_label": historic_labels.get(historic, "Nao mapeado"),
                    "jnl_oai_status": oai_status,
                    "jnl_oai_status_label": oai_labels.get(oai_status, "Nao mapeado"),
                    "total": total,
                }
            )

        result = {
            "success": True,
            "total": len(rows),
            "source_source": rows,
            "summary": {
                "jnl_oai_status": summary_oai,
                "jnl_historic": summary_historic,
                "jnl_historic_x_jnl_oai_status": enriched_combined,
            },
        }

        if silent:
            return result

        if not rows:
            console.print("[yellow]Nenhum registro encontrado em source_source.[/yellow]")
            return None

        sum_combined_table = Table(
            title="Sumario consolidado - historic x oai_status",
            header_style="bold yellow",
            show_lines=True,
        )
        sum_combined_table.add_column("jnl_historic")
        sum_combined_table.add_column("tipo")
        sum_combined_table.add_column("jnl_oai_status")
        sum_combined_table.add_column("status")
        sum_combined_table.add_column("total", justify="right")

        for item in enriched_combined:
            sum_combined_table.add_row(
                item["jnl_historic"],
                item["jnl_historic_label"],
                item["jnl_oai_status"],
                item["jnl_oai_status_label"],
                str(item["total"]),
            )

        console.print(sum_combined_table)
        console.print(f"[bold green]Total:[/bold green] {len(rows)}")

        return None

    except Exception as e:
        if silent:
            return erro(str(e))

        console.print(f"[bold red]Erro ao listar source_source:[/bold red] {e}")
        return False

    finally:
        if conn is not None:
            conn.close()


def source_list_status(status, silent=False):
    conn = None

    if status is None:
        if silent:
            return erro("Informe o status. Exemplo: SOURCE LIST 100")

        console.print("[bold red]Informe o status. Exemplo: SOURCE LIST 100[/bold red]")
        return False

    status_value = str(status).strip()

    if status_value == "":
        if silent:
            return erro("Informe o status. Exemplo: SOURCE LIST 100")

        console.print("[bold red]Informe o status. Exemplo: SOURCE LIST 100[/bold red]")
        return False

    try:
        conn = get_connection("brapci")

        with conn.cursor() as cur:
            cur.execute(
                """
                SELECT
                    jnl_name,
                    jnl_oai_status,
                    jnl_frbr
                FROM source_source
                WHERE jnl_oai_status = %s
                ORDER BY jnl_name
                """,
                (status_value,),
            )
            rows = cur.fetchall()

        for row in rows:
            frbr = str(row.get("jnl_frbr", "")).strip()
            row["link"] = f"https://brapci.inf.br/v/{frbr}" if frbr else ""

        result = {
            "success": True,
            "status": status_value,
            "total": len(rows),
            "sources": rows,
        }

        if silent:
            return result

        if not rows:
            console.print(
                f"[yellow]Nenhuma revista encontrada para jnl_oai_status={status_value}.[/yellow]"
            )
            return None

        table = Table(
            title=f"SOURCES - Status {status_value}",
            header_style="bold cyan",
            show_lines=True,
        )
        table.add_column("jnl_name", overflow="fold")
        table.add_column("jnl_oai_status", justify="right")
        table.add_column("link", overflow="fold")

        for row in rows:
            table.add_row(
                str(row.get("jnl_name", "")),
                str(row.get("jnl_oai_status", "")),
                str(row.get("link", "")),
            )

        console.print(table)
        console.print(f"[bold green]Total:[/bold green] {len(rows)}")
        return None

    except Exception as e:
        if silent:
            return erro(str(e))

        console.print(f"[bold red]Erro ao listar revistas por status:[/bold red] {e}")
        return False

    finally:
        if conn is not None:
            conn.close()


def run(parametros=None, chat=None, silent=False):
    if parametros is None:
        parametros = []

    if not silent:
        console.print()
        console.rule("[bold blue]SOURCES[/bold blue]")

    if len(parametros) == 0:
        parametros = []
        parametros.append('')

    print(f"==========", parametros[0].upper())

    if parametros[0].upper() == "":
        return source_list(silent=silent)

    if parametros[0].upper() == "LIST":
        if len(parametros) < 2:
            if silent:
                return erro("Informe o status. Exemplo: SOURCE LIST 100")

            console.print("[bold red]Informe o status. Exemplo: SOURCE LIST 100[/bold red]")
            return False

        return source_list_status(parametros[1],silent=silent)

    if silent:
        return erro("Tipo de tarefa inválido. Use SOURCE LIST.")

    console.print("[bold red]Tipo de tarefa inválido. Use SOURCE LIST.[/bold red]")
    return False


if __name__ == "__main__":
    run(parametros=sys.argv[2:], silent=False)
