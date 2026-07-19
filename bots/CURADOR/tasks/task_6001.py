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


def buscar(nome):
    """
    Pesquisa uma instituição no ROR.
    """

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
    """
    Compara duas strings ignorando maiúsculas/minúsculas.
    """

    return (
        str(a).strip().casefold()
        ==
        str(b).strip().casefold()
    )


def localizar(resultado, nome):
    """
    Localiza a melhor correspondência.
    """

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

    #
    # Retorna o primeiro encontrado
    #

    if itens:

        return itens[0]

    return None


def nome_principal(item):
    """
    Retorna o nome principal da instituição.
    """

    nomes = item.get(
        "names",
        []
    )

    #
    # Nome oficial do ROR
    #

    for nome in nomes:

        if "ror_display" in nome.get(
            "types",
            []
        ):

            return nome.get(
                "value",
                ""
            )

    #
    # Português
    #

    for nome in nomes:

        if nome.get("lang") == "pt":

            return nome.get(
                "value",
                ""
            )

    #
    # Inglês
    #

    for nome in nomes:

        if nome.get("lang") == "en":

            return nome.get(
                "value",
                ""
            )

    #
    # Primeiro disponível
    #

    if nomes:

        return nomes[0].get(
            "value",
            ""
        )

    return ""


def sigla(item):
    """
    Retorna a sigla.
    """

    for nome in item.get(
        "names",
        []
    ):

        if "acronym" in nome.get(
            "types",
            []
        ):

            return nome.get(
                "value",
                ""
            )

    return ""


def pais(item):

    for local in item.get(
        "locations",
        []
    ):

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

    for local in item.get(
        "locations",
        []
    ):

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

    for local in item.get(
        "locations",
        []
    ):

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


def run(parametros=None, chat=None):

    if parametros is None:

        parametros = []

    console.print()

    console.rule(
        "[bold blue]ROR[/bold blue]"
    )

    if len(parametros) == 0:

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

        dados = buscar(nome)

    except Exception as e:

        console.print(
            f"[red]Erro ao consultar o ROR:[/red] {e}"
        )

        return False

    item = localizar(
        dados,
        nome
    )

    if item is None:

        console.print()

        console.print(
            "[yellow]Instituição não encontrada.[/yellow]"
        )

        return False

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

        nome_principal(item),

        sigla(item),

        cidade(item),

        estado(item),

        pais(item),

        item.get(
            "id",
            ""
        ),

        item.get(
            "status",
            ""
        )

    )

    console.print(table)

    return item