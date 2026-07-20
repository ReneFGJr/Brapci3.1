#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Task 4003
Limpa todos os IDs armazenados.
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
    "id": 4003,
    "name": "Clear",
    "description": "Remove todos os IDs armazenados.",
    "patterns": [
        "clear",
        "clear ids",
        "limpar",
        "reset"
    ],
    "parameters": []
}


def erro(mensagem):

    return {
        "success": False,
        "error": mensagem
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


def run(
    parametros=None,
    chat=None,
    silent=False
):

    global IDs

    if parametros is None:

        parametros = []

    if not silent:

        console.print()

        console.rule(
            "[bold red]CLEAR[/bold red]"
        )

    #
    # Carrega os IDs atuais
    #

    try:

        carregar_ids()

    except Exception as e:

        if silent:

            return erro(
                str(e)
            )

        console.print(
            f"[red]Erro ao carregar IDs:[/red] {e}"
        )

        return False

    quantidade = len(IDs)

    #
    # Limpa a lista
    #

    IDs.clear()

    #
    # Salva o arquivo atualizado
    #

    try:

        salvar_ids()

    except Exception as e:

        if silent:

            return erro(
                str(e)
            )

        console.print(
            f"[red]Erro ao salvar arquivo:[/red] {e}"
        )

        return False

    resultado = {

        "success": True,

        "removed": quantidade,

        "total": 0,

        "arquivo": str(
            get_arquivo()
        ),

        "ids": []

    }

    if silent:

        return resultado

    console.print(
        f"[bold green]✔ {quantidade} IDs removidos.[/bold green]"
    )

    console.print(
        "[green]Lista de IDs vazia.[/green]"
    )

    console.print(
        f"[cyan]Arquivo:[/cyan] {get_arquivo()}"
    )

    return resultado