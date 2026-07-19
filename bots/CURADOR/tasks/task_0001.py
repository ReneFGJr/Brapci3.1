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


def baixar_json(url: str, arquivo: Path, titulo: str):

    console.print()
    console.print(f"[bold cyan]{titulo}[/bold cyan]")

    CACHE.mkdir(parents=True, exist_ok=True)

    console.print(f"[yellow]Endpoint:[/yellow] {url}")

    try:

        resposta = requests.get(
            url,
            timeout=60
        )

    except Exception as e:

        console.print(f"[bold red]Erro de conexão:[/bold red] {e}")
        return False

    if resposta.status_code != 200:

        console.print(
            f"[bold red]Erro HTTP {resposta.status_code}[/bold red]"
        )
        return False

    try:

        dados = resposta.json()

    except Exception:

        console.print(
            "[bold red]O endpoint não retornou um JSON válido.[/bold red]"
        )
        return False

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

    console.print(
        f"[green]✔ Arquivo salvo:[/green] {arquivo}"
    )

    if isinstance(dados, list):

        console.print(
            f"[green]✔ Registros:[/green] {len(dados)}"
        )

    elif isinstance(dados, dict):

        console.print(
            f"[green]✔ Objetos:[/green] {len(dados.keys())}"
        )

    console.print(
        "[bold green]✔ Atualização concluída[/bold green]"
    )

    return True


def atualizar_revistas():

    return baixar_json(
        URL_JOURNALS,
        ARQ_JOURNALS,
        "Atualizando revistas da BRAPCI"
    )


def atualizar_eventos():

    return baixar_json(
        URL_EVENTS,
        ARQ_EVENTS,
        "Atualizando eventos da BRAPCI"
    )


def run(parametros=None, chat=None):

    console.rule("[bold blue]CURADOR - Atualização da Base Local[/bold blue]")

    ok1 = atualizar_revistas()

    ok2 = atualizar_eventos()

    console.rule()

    if ok1 and ok2:

        console.print(
            "[bold green]✓ Cache da BRAPCI atualizado com sucesso.[/bold green]"
        )
        return "Atualização concluída."

    console.print(
        "[bold red]⚠ A atualização foi concluída com erros.[/bold red]"
    )

    return "Atualização concluída com erros."