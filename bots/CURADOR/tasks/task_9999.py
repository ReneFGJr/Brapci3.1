#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Task 9999
Mostra a ajuda do CURADOR.
"""

from pathlib import Path
import json

from rich.console import Console
from rich.table import Table

console = Console()

TASK = {
    "id": 9999,
    "name": "Ajuda",
    "description": "Mostra a ajuda do CURADOR.",
    "patterns": [
        "help",
        "ajuda",
        "como usar",
        "ajuda curador"
    ],
    "parameters": [
        {
            "name": "fonte",
            "type": "string",
            "required": False
        }
    ]
}


def erro(mensagem):

    return {
        "success": False,
        "error": mensagem
    }


def ajuda(silent=False):

    arquivo = Path("data/intents.json")

    if not arquivo.exists():

        if silent:

            return erro(
                "Arquivo data/intents.json não encontrado."
            )

        console.print(
            "[bold red]Arquivo data/intents.json não encontrado.[/bold red]"
        )

        return False

    try:

        with open(
            arquivo,
            "r",
            encoding="utf-8"
        ) as f:

            intents = json.load(f)

    except Exception as e:

        if silent:

            return erro(
                str(e)
            )

        console.print(
            f"[bold red]Erro ao ler intents.json:[/bold red] {e}"
        )

        return False

    comandos = []

    for codigo in sorted(intents.keys()):

        intent = intents[codigo]

        comandos.append({

            "codigo": codigo.strip(),

            "nome": intent.get(
                "name",
                ""
            ),

            "descricao": intent.get(
                "description",
                ""
            ),

            "patterns": intent.get(
                "patterns",
                []

            )

        })

    resultado = {

        "success": True,

        "total": len(
            comandos
        ),

        "commands": comandos

    }

    #
    # Modo silencioso:
    # não imprime absolutamente nada.
    #

    if silent:

        return resultado

    #
    # Interface Rich
    #

    table = Table(
        title="CURADOR - Comandos Disponíveis",
        header_style="bold cyan",
        show_lines=True
    )

    table.add_column(
        "Código",
        style="yellow",
        width=8
    )

    table.add_column(
        "Descrição",
        style="green",
        width=35
    )

    table.add_column(
        "Exemplos",
        style="white"
    )

    for comando in comandos:

        table.add_row(

            comando["codigo"],

            comando["descricao"],

            ", ".join(
                comando["patterns"][:3]
            )

        )

    console.print()

    console.print(table)

    console.print()

    console.print(
        "[bold cyan]Exemplo:[/bold cyan]"
    )

    console.print(
        "  CURADOR > atualizar"
    )

    console.print(
        "  CURADOR > coletar revistas"
    )

    console.print(
        "  CURADOR > gerar palavras-chave"
    )

    console.print()

    #
    # Em modo interativo não retorna o JSON,
    # evitando que o CURADOR.py o imprima.
    #

    return None


def run(
    parametros=None,
    chat=None,
    silent=False
):

    if parametros is None:

        parametros = []

    return ajuda(
        silent=silent
    )