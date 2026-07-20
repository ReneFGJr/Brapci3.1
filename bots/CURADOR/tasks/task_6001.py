#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Task 6001
Consulta uma instituição no ROR.
"""

import requests

from rich.console import Console
from rich.table import Table

console = Console()

TASK = {
    "id": 6001,
    "name": "ROR",
    "description": "Consulta uma instituição no ROR.",
    "patterns": [
        "ror"
    ],
    "parameters": [
        {
            "name": "instituicao",
            "type": "string",
            "required": True
        }
    ]
}

URL = "https://api.ror.org/v2/organizations"

HEADERS = {
    "Accept": "application/json"
}


def buscar(nome, silent=False):
    """
    Pesquisa uma instituição no ROR.
    """

    if not silent:

        console.print(
            f"[bold blue]Consultando ROR:[/bold blue] {nome}"
        )

    r = requests.get(
        URL,
        params={
            "query": nome
        },
        headers=HEADERS,
        timeout=20
    )

    r.raise_for_status()

    return r.json()


def igual(a, b):

    return (
        str(a).strip().casefold()
        ==
        str(b).strip().casefold()
    )


def localizar(resultado, nome):

    nome = nome.strip()

    itens = resultado.get("items", [])

    #
    # Correspondência exata
    #

    for item in itens:

        for registro in item.get("names", []):

            if igual(
                registro.get("value", ""),
                nome
            ):

                return item

    #
    # Acrônimos e aliases
    #

    for item in itens:

        for registro in item.get("names", []):

            tipos = registro.get(
                "types",
                []
            )

            if (
                "acronym" in tipos
                or
                "alias" in tipos
            ):

                if igual(
                    registro.get("value", ""),
                    nome
                ):

                    return item

    if itens:

        return itens[0]

    return None


def nome_principal(item):

    nomes = item.get("names", [])

    for nome in nomes:

        if "ror_display" in nome.get("types", []):

            return nome.get("value", "")

    for nome in nomes:

        if nome.get("lang") == "pt":

            return nome.get("value", "")

    for nome in nomes:

        if nome.get("lang") == "en":

            return nome.get("value", "")

    if nomes:

        return nomes[0].get("value", "")

    return ""


def sigla(item):

    for nome in item.get("names", []):

        if "acronym" in nome.get("types", []):

            return nome.get("value", "")

    return ""


def pais(item):

    for local in item.get("locations", []):

        geo = local.get(
            "geonames_details",
            {}
        )

        if geo:

            return geo.get(
                "country_name",
                ""
            )

    return ""


def estado(item):

    for local in item.get("locations", []):

        geo = local.get(
            "geonames_details",
            {}
        )

        if geo:

            return geo.get(
                "country_subdivision_name",
                ""
            )

    return ""


def cidade(item):

    for local in item.get("locations", []):

        geo = local.get(
            "geonames_details",
            {}
        )

        if geo:

            return geo.get(
                "name",
                ""
            )

    return ""


def erro(mensagem):

    return {
        "success": False,
        "error": mensagem
    }


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
            "[bold blue]ROR[/bold blue]"
        )

    if len(parametros) == 0:

        if silent:

            return erro(
                "Informe uma instituição."
            )

        console.print(
            "[bold red]Informe uma instituição.[/bold red]"
        )

        console.print()

        console.print(
            "Exemplo:"
        )

        console.print(
            "    ror UFRGS"
        )

        console.print(
            "    ror Universidade Federal do Rio Grande do Sul"
        )

        return False

    nome = " ".join(parametros)

    try:

        dados = buscar(
            nome,
            silent=silent
        )

    except Exception as e:

        if silent:

            return erro(str(e))

        console.print(
            f"[red]Erro ao consultar o ROR:[/red] {e}"
        )

        return False

    item = localizar(
        dados,
        nome
    )

    if item is None:

        if silent:

            return erro(
                "Instituição não encontrada."
            )

        console.print()

        console.print(
            "[yellow]Instituição não encontrada.[/yellow]"
        )

        return False

    resultado = {

        "success": True,

        "nome": nome_principal(item),

        "sigla": sigla(item),

        "cidade": cidade(item),

        "estado": estado(item),

        "pais": pais(item),

        "ror": item.get(
            "id",
            ""
        ),

        "status": item.get(
            "status",
            ""
        )

    }

    if silent:

        return resultado

    table = Table()

    table.add_column(
        "Nome",
        style="cyan"
    )

    table.add_column(
        "Sigla",
        style="white"
    )

    table.add_column(
        "Cidade",
        style="green"
    )

    table.add_column(
        "Estado",
        style="green"
    )

    table.add_column(
        "País",
        style="magenta"
    )

    table.add_column(
        "ROR",
        style="yellow"
    )

    table.add_column(
        "Status",
        style="red"
    )

    table.add_row(

        resultado["nome"],

        resultado["sigla"],

        resultado["cidade"],

        resultado["estado"],

        resultado["pais"],

        resultado["ror"],

        resultado["status"]

    )

    console.print(table)

    return resultado