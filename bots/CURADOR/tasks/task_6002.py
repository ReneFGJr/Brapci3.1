#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Task 6002
Consulta metadados de DOI (Crossref / DataCite)
"""

import requests

from rich.console import Console
from rich.table import Table
from rich.panel import Panel

console = Console()

TASK = {
    "id": 6002,
    "name": "DOI",
    "description":
    "Consulta metadados de um DOI utilizando Crossref e DataCite.",
    "patterns": ["doi", "crossref", "datacite", "metadata", "metadados"],
    "parameters": [{
        "name": "doi",
        "type": "string",
        "required": True
    }]
}

URL_CROSSREF = "https://api.crossref.org/works/{}"
URL_DATACITE = "https://api.datacite.org/dois/{}"

###############################################################################
# Utilidades
###############################################################################


def erro(msg):
    return {"success": False, "error": msg}


def primeiro(lista):
    return lista[0] if lista else ""


def autores_crossref(authors):

    nomes = []

    for a in authors:
        nome = " ".join(filter(None, [a.get("given"), a.get("family")]))
        nomes.append(nome)

    return nomes


def autores_datacite(creators):

    nomes = []

    for a in creators:
        nomes.append(a.get("name", ""))

    return nomes


###############################################################################
# Crossref
###############################################################################


def consulta_crossref(doi):

    url = URL_CROSSREF.format(doi)

    r = requests.get(url, timeout=30, headers={"User-Agent": "CURADOR/1.0"})

    if r.status_code != 200:
        return None

    w = r.json()["message"]

    return {
        "success": True,
        "source": "Crossref",
        "doi": w.get("DOI"),
        "title": primeiro(w.get("title", [])),
        "subtitle": primeiro(w.get("subtitle", [])),
        "authors": autores_crossref(w.get("author", [])),
        "publisher": w.get("publisher"),
        "journal": primeiro(w.get("container-title", [])),
        "type": w.get("type"),
        "language": w.get("language"),
        "year": (w.get("issued", {}).get("date-parts", [[None]])[0][0]),
        "volume": w.get("volume"),
        "issue": w.get("issue"),
        "pages": w.get("page"),
        "url": w.get("URL"),
        "abstract": w.get("abstract")
    }


###############################################################################
# DataCite
###############################################################################


def consulta_datacite(doi):

    url = URL_DATACITE.format(doi)

    r = requests.get(url, timeout=30, headers={"User-Agent": "CURADOR/1.0"})

    if r.status_code != 200:
        return None

    att = r.json()["data"]["attributes"]

    return {
        "success": True,
        "source": "DataCite",
        "doi": att.get("doi"),
        "title": primeiro(att.get("titles", [{}])).get("title"),
        "authors": autores_datacite(att.get("creators", [])),
        "publisher": att.get("publisher"),
        "journal": None,
        "type": att.get("types", {}).get("resourceTypeGeneral"),
        "language": att.get("language"),
        "year": att.get("publicationYear"),
        "volume": None,
        "issue": None,
        "pages": None,
        "url": att.get("url"),
        "abstract": att.get("descriptions")
    }


###############################################################################
# Exibição
###############################################################################


def mostrar(metadata):

    console.print()

    console.print(
        Panel.fit("[bold cyan]Metadados do DOI[/bold cyan]",
                  border_style="cyan"))

    table = Table(show_header=False)

    table.add_column(style="bold green", width=20)
    table.add_column(style="white")

    for campo in [
            "source", "doi", "title", "publisher", "journal", "type", "year",
            "volume", "issue", "pages", "language", "url"
    ]:

        valor = metadata.get(campo)

        if valor:
            table.add_row(campo.upper(), str(valor))

    if metadata.get("authors"):

        table.add_row("AUTHORS", "\n".join(metadata["authors"]))

    console.print(table)

    if metadata.get("abstract"):

        console.print()

        console.print(
            Panel(str(metadata["abstract"]),
                  title="[yellow]Abstract[/yellow]"))


###############################################################################
# Run
###############################################################################


def run(parametros=None, chat=None, silent=False):

    parametros = parametros or []

    if len(parametros) == 0:

        resultado = erro("Informe um DOI.")

        if silent:
            return resultado

        console.print("[red]Erro:[/red]", resultado["error"])

        return resultado

    doi = parametros[0].strip()

    try:

        resultado = consulta_crossref(doi)

        if resultado is None:
            resultado = consulta_datacite(doi)

        if resultado is None:
            resultado = erro("DOI não encontrado.")

        if silent:
            return resultado

        if resultado["success"]:
            mostrar(resultado)
        else:
            console.print(f"[red]{resultado['error']}[/red]")

        return resultado

    except Exception as e:

        resultado = erro(str(e))

        if silent:
            return resultado

        console.print(f"[red]{e}[/red]")

        return resultado
