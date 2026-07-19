#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Task 4001
Carrega a lista temporária de IDs de artigos.
"""

from pathlib import Path
import json
import socket

from rich.console import Console
from rich.table import Table

console = Console()

TMP = Path(".tmp")

#
# Variável global
#
IDs = []


TASK = {
    "id": 4001,
    "name": "Get",
    "description": "Carrega a lista de IDs armazenada.",
    "patterns": [
        "get",
        "load",
        "carregar",
        "carregar ids"
    ],
    "parameters": []
}


def get_ip():

    try:

        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)

        s.connect(("8.8.8.8", 80))

        ip = s.getsockname()[0]

        s.close()

    except Exception:

        ip = "127.0.0.1"

    return ip.replace(".", "_")


def carregar_ids():

    global IDs

    arquivo = TMP / f"var_{get_ip()}.json"

    if not arquivo.exists():

        console.print(
            "[bold red]Arquivo de IDs não encontrado.[/bold red]"
        )

        return []

    try:

        with open(
            arquivo,
            "r",
            encoding="utf-8"
        ) as f:

            IDs = json.load(f)

    except Exception as e:

        console.print(
            f"[bold red]Erro ao carregar arquivo:[/bold red] {e}"
        )

        return []

    console.print(
        f"[bold green]✔ {len(IDs)} IDs carregados.[/bold green]"
    )

    return IDs


def mostrar_ids():

    if len(IDs) == 0:

        console.print(
            "[yellow]Nenhum ID carregado.[/yellow]"
        )

        return

    table = Table(
        title="IDs em Memória",
        header_style="bold cyan"
    )

    table.add_column("#", style="yellow", width=6)
    table.add_column("ID", style="green")

    for k, item in enumerate(IDs, start=1):

        table.add_row(
            str(k),
            str(item)
        )

    console.print(table)


def run(parametros=None, chat=None):

    console.rule("[bold blue]GET[/bold blue]")

    carregar_ids()

    mostrar_ids()

    return IDs