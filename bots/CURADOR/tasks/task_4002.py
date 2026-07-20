#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Task 4002
Remove um ou mais IDs da lista temporária.
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
    "id": 4002,
    "name": "Remove",
    "description": "Remove um ou mais IDs da lista temporária.",
    "patterns": [
        "remove",
        "del",
        "delete",
        "rm"
    ],
    "parameters": [
        {
            "name": "ids",
            "type": "array",
            "required": True
        }
    ]
}


def erro(mensagem):

    return {
        "success": False,
        "error": mensagem
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
            "[bold red]REMOVE[/bold red]"
        )

    if len(parametros) == 0:

        if silent:

            return erro(
                "Nenhum ID informado."
            )

        console.print(
            "[bold red]Nenhum ID informado.[/bold red]"
        )

        console.print()

        console.print(
            "Exemplo:"
        )

        console.print(
            "    remove 123 456"
        )

        return False

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

    removidos = 0

    invalidos = []

    nao_encontrados = []

    for item in parametros:

        try:

            valor = int(item)

            if valor in IDs:

                IDs.remove(valor)

                removidos += 1

            else:

                nao_encontrados.append(
                    valor
                )

                if not silent:

                    console.print(
                        f"[yellow]ID não encontrado:[/yellow] {valor}"
                    )

        except ValueError:

            invalidos.append(
                item
            )

            if not silent:

                console.print(
                    f"[yellow]Valor inválido:[/yellow] {item}"
                )

    IDs.sort()

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

        "removed": removidos,

        "total": len(IDs),

        "invalidos": invalidos,

        "nao_encontrados": nao_encontrados,

        "arquivo": str(
            get_arquivo()
        ),

        "ids": IDs

    }

    if silent:

        return resultado

    console.print()

    console.print(
        f"[bold green]✔ {removidos} IDs removidos.[/bold green]"
    )

    console.print(
        f"[bold cyan]Total de IDs:[/bold cyan] {len(IDs)}"
    )

    if nao_encontrados:

        console.print(
            f"[yellow]IDs não encontrados:[/yellow] {nao_encontrados}"
        )

    if invalidos:

        console.print(
            f"[yellow]Valores inválidos:[/yellow] {invalidos}"
        )

    console.print(
        f"[green]IDs:[/green] {IDs}"
    )

    console.print(
        f"[cyan]Arquivo:[/cyan] {get_arquivo()}"
    )

    return resultado