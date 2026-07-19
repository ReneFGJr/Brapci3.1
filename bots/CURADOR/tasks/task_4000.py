#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Task 4000
Armazena uma lista temporária de IDs de artigos.
"""

from pathlib import Path
import json
import socket

from rich.console import Console

console = Console()

TMP = Path(".tmp")

#
# Variável em memória
#
IDs = []

TASK = {
    "id": 4000,
    "name": "Set",
    "description": "Armazena uma lista temporária de IDs de artigos.",
    "patterns": [
        "set"
    ],
    "parameters": [
        {
            "name": "ids",
            "type": "array",
            "required": True
        }
    ]
}


def get_ip():
    """
    Retorna o IP local da máquina.
    """

    try:

        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        s.connect(("8.8.8.8", 80))

        ip = s.getsockname()[0]

        s.close()

    except Exception:

        ip = "127.0.0.1"

    return ip.replace(".", "_")


def get_arquivo():

    TMP.mkdir(
        parents=True,
        exist_ok=True
    )

    return TMP / f"var_{get_ip()}.json"


def carregar_ids():

    global IDs

    arquivo = get_arquivo()

    IDs = []

    if arquivo.exists():

        try:

            with open(
                arquivo,
                "r",
                encoding="utf-8"
            ) as f:

                IDs = json.load(f)

        except Exception:

            IDs = []

    return IDs


def salvar_ids():

    arquivo = get_arquivo()

    with open(
        arquivo,
        "w",
        encoding="utf-8"
    ) as f:

        json.dump(
            IDs,
            f,
            ensure_ascii=False,
            indent=4
        )


def run(parametros=None, chat=None):

    global IDs

    if parametros is None:
        parametros = []

    console.print()
    console.rule("[bold blue]SET[/bold blue]")

    if len(parametros) == 0:

        console.print(
            "[bold red]Nenhum ID informado.[/bold red]"
        )

        console.print("Exemplo:")
        console.print("    set 123 456 789")

        return False

    #
    # Carrega os IDs já existentes
    #

    carregar_ids()

    novos = 0

    for item in parametros:

        try:

            valor = int(item)

            if valor not in IDs:

                IDs.append(valor)
                novos += 1

        except ValueError:

            console.print(
                f"[yellow]Ignorando valor inválido:[/yellow] {item}"
            )

    #
    # Ordena os IDs
    #

    IDs.sort()

    #
    # Salva novamente
    #

    salvar_ids()

    console.print(
        f"[bold green]✔ {novos} novos IDs adicionados.[/bold green]"
    )

    console.print(
        f"[bold cyan]Total de IDs:[/bold cyan] {len(IDs)}"
    )

    console.print(
        f"[green]IDs:[/green] {IDs}"
    )

    console.print(
        f"[cyan]Arquivo:[/cyan] {get_arquivo()}"
    )

    return IDs