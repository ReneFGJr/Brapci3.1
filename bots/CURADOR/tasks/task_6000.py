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


def buscar(nome):
    """
    Pesquisa pesquisadores pelo nome.
    """

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

    #
    # Apenas uma instituição
    #

    if isinstance(instituicoes, str):

        return instituicoes

    #
    # Lista de instituições
    #

    if isinstance(instituicoes, list):

        if len(instituicoes):

            return instituicoes[-1]

    return ""


def run(parametros=None, chat=None):

    if parametros is None:
        parametros = []

    console.print()

    console.rule("[bold blue]ORCID[/bold blue]")

    if len(parametros) == 0:

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

    nome = " ".join(parametros).strip()

    try:

        dados = buscar(nome)

    except Exception as e:

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

        credit = item.get("credit-name") or ""

        given = item.get("given-names") or ""

        family = item.get("family-names") or ""

        nome_orcid = credit.strip()

        if not nome_orcid:

            nome_orcid = f"{given} {family}".strip()

        #
        # Comparação exata
        #

        if nome_orcid.lower() != nome.lower():

            continue

        encontrados.append({

            "nome": nome_orcid,

            "orcid": item.get("orcid-id") or "",

            "instituicao": ultima_instituicao(item)

        })

    if len(encontrados) == 0:

        console.print()

        console.print(
            "[yellow]Nenhum pesquisador encontrado.[/yellow]"
        )

        return False

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

    console.print(table)

    return encontrados