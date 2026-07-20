#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Task 6000
Consulta pesquisador no ORCID.
"""

import requests

from rich.console import Console
from rich.table import Table

console = Console()

TASK = {
    "id": 6000,
    "name": "ORCID",
    "description": "Consulta um pesquisador no ORCID.",
    "patterns": [
        "orcid"
    ],
    "parameters": [
        {
            "name": "nome",
            "type": "string",
            "required": True
        }
    ]
}

URL = "https://pub.orcid.org/v3.0/expanded-search/"

HEADERS = {
    "Accept": "application/json"
}


def erro(mensagem):

    return {
        "success": False,
        "error": mensagem
    }


def buscar(nome, silent=False):
    """
    Pesquisa pesquisadores pelo nome.
    """

    if not silent:

        console.print(
            f"[bold blue]Consultando ORCID:[/bold blue] {nome}"
        )

    r = requests.get(
        URL,
        params={
            "q": f'"{nome}"'
        },
        headers=HEADERS,
        timeout=20
    )

    r.raise_for_status()

    return r.json()


def ultima_instituicao(item):
    """
    Retorna a última instituição do pesquisador.
    """

    instituicoes = item.get("institution-name")

    if instituicoes is None:

        return ""

    if isinstance(instituicoes, str):

        return instituicoes

    if isinstance(instituicoes, list):

        if len(instituicoes):

            return instituicoes[-1]

    return ""


def run(
    parametros=None,
    chat=None,
    silent=False
):

    if parametros is None:

        parametros = []

    if not silent:

        console.print()

        console.rule(
            "[bold blue]ORCID[/bold blue]"
        )

    if len(parametros) == 0:

        if silent:

            return erro(
                "Informe o nome do pesquisador."
            )

        console.print(
            "[bold red]Informe o nome do pesquisador.[/bold red]"
        )

        console.print()

        console.print(
            "Exemplo:"
        )

        console.print(
            "    orcid Rene Faustino Gabriel Junior"
        )

        return False

    nome = " ".join(
        parametros
    ).strip()

    try:

        dados = buscar(
            nome,
            silent=silent
        )

    except Exception as e:

        if silent:

            return erro(
                str(e)
            )

        console.print(
            f"[red]Erro ao consultar ORCID:[/red] {e}"
        )

        return False

    resultados = dados.get(
        "expanded-result",
        []
    )

    encontrados = []

    for item in resultados:

        credit = item.get(
            "credit-name"
        ) or ""

        given = item.get(
            "given-names"
        ) or ""

        family = item.get(
            "family-names"
        ) or ""

        nome_orcid = credit.strip()

        if not nome_orcid:

            nome_orcid = (
                f"{given} {family}"
            ).strip()

        #
        # Comparação exata
        #

        if nome_orcid.lower() != nome.lower():

            continue

        encontrados.append({

            "nome": nome_orcid,

            "orcid": item.get(
                "orcid-id",
                ""
            ),

            "instituicao": ultima_instituicao(item)

        })

    if len(encontrados) == 0:

        if silent:

            return erro(
                "Nenhum pesquisador encontrado."
            )

        console.print()

        console.print(
            "[yellow]Nenhum pesquisador encontrado.[/yellow]"
        )

        return False

    resultado = {

        "success": True,

        "total": len(
            encontrados
        ),

        "results": encontrados

    }

    if silent:

        return resultado

    table = Table()

    table.add_column(
        "Nome",
        style="cyan"
    )

    table.add_column(
        "ORCID",
        style="green"
    )

    table.add_column(
        "Instituição",
        style="magenta"
    )

    for item in encontrados:

        table.add_row(

            item["nome"],

            item["orcid"],

            item["instituicao"]

        )

    console.print(
        table
    )

    return resultado