#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Task 0001
Atualização dos arquivos de cache da BRAPCI
"""

from pathlib import Path
import json
import requests

from rich.console import Console

console = Console()

CACHE = Path(".cache")

URL_JOURNALS = "https://cip.brapci.inf.br/api/brapci/source/journal"
URL_EVENTS   = "https://cip.brapci.inf.br/api/brapci/source/E"

ARQ_JOURNALS = CACHE / "journals.json"
ARQ_EVENTS   = CACHE / "events.json"

TASK = {
    "id": 1,
    "name": "Atualização de Cache",
    "description": "Atualiza os arquivos locais utilizados pelo CURADOR.",
    "patterns": [
        "atualizar",
        "atualizar revistas",
        "atualizar eventos",
        "atualizar listas",
        "atualizar periódicos",
        "atualizar cache"
    ],
    "parameters": []
}


def erro(mensagem):

    return {
        "success": False,
        "error": mensagem
    }


def baixar_json(
    url: str,
    arquivo: Path,
    titulo: str,
    silent=False
):

    if not silent:

        console.print()

        console.print(
            f"[bold cyan]{titulo}[/bold cyan]"
        )

        console.print(
            f"[yellow]Endpoint:[/yellow] {url}"
        )

    CACHE.mkdir(
        parents=True,
        exist_ok=True
    )

    try:

        resposta = requests.get(
            url,
            timeout=60
        )

    except Exception as e:

        return erro(
            f"Erro de conexão: {e}"
        )

    if resposta.status_code != 200:

        return erro(
            f"Erro HTTP {resposta.status_code}"
        )

    try:

        dados = resposta.json()

    except Exception:

        return erro(
            "O endpoint não retornou um JSON válido."
        )

    try:

        with open(
            arquivo,
            "w",
            encoding="utf-8"
        ) as f:

            json.dump(
                dados,
                f,
                ensure_ascii=False,
                indent=4
            )

    except Exception as e:

        return erro(
            f"Erro ao gravar arquivo: {e}"
        )

    total = 0

    if isinstance(
        dados,
        list
    ):

        total = len(dados)

    elif isinstance(
        dados,
        dict
    ):

        total = len(dados)

    if not silent:

        console.print(
            f"[green]✔ Arquivo salvo:[/green] {arquivo}"
        )

        console.print(
            f"[green]✔ Registros:[/green] {total}"
        )

        console.print(
            "[bold green]✔ Atualização concluída[/bold green]"
        )

    return {

        "success": True,

        "arquivo": str(
            arquivo
        ),

        "url": url,

        "total": total

    }


def atualizar_revistas(
    silent=False
):

    return baixar_json(

        URL_JOURNALS,

        ARQ_JOURNALS,

        "Atualizando revistas da BRAPCI",

        silent=silent

    )


def atualizar_eventos(
    silent=False
):

    return baixar_json(

        URL_EVENTS,

        ARQ_EVENTS,

        "Atualizando eventos da BRAPCI",

        silent=silent

    )


def run(
    parametros=None,
    chat=None,
    silent=False
):

    if parametros is None:

        parametros = []

    if not silent:

        console.rule(
            "[bold blue]CURADOR - Atualização da Base Local[/bold blue]"
        )

    revistas = atualizar_revistas(
        silent=silent
    )

    eventos = atualizar_eventos(
        silent=silent
    )

    sucesso = (

        revistas.get(
            "success",
            False
        )

        and

        eventos.get(
            "success",
            False
        )

    )

    resultado = {

        "success": sucesso,

        "journals": revistas,

        "events": eventos

    }

    if silent:

        return resultado

    console.rule()

    if sucesso:

        console.print(

            "[bold green]✓ Cache da BRAPCI atualizado com sucesso.[/bold green]"

        )

    else:

        console.print(

            "[bold red]⚠ A atualização foi concluída com erros.[/bold red]"

        )

        if not revistas.get("success"):

            console.print(
                f"[red]Revistas:[/red] {revistas['error']}"
            )

        if not eventos.get("success"):

            console.print(
                f"[red]Eventos:[/red] {eventos['error']}"
            )

    return []