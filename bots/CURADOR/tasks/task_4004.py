#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Task 4004
Sincroniza palavras-chave na tabela brapci_vc.vc_term.
"""

from rich.console import Console

from lib.database import conectar
from tasks import task_0200

console = Console()

TASK = {
    "id": 4004,
    "name": "Sync VC Terms",
    "description": "Lê palavras-chave e insere novos termos em brapci_vc.vc_term.",
    "patterns": [
        "vc",
        "vc term",
        "vc terms",
        "sync vc",
        "sync keyword"
    ],
    "parameters": []
}

def erro(mensagem):

    return {
        "success": False,
        "error": mensagem
    }


def normalizar_termo(termo):

    if termo is None:
        return ""

    return " ".join(
        str(termo).split()
    ).strip()


def extrair_keywords(payload):

    if isinstance(payload, dict):

        return payload.get(
            "data",
            []
        )

    if isinstance(payload, list):

        return payload

    return []


def montar_v_erro(termo):

    if task_0200.has_encoding_problem(termo):

        return termo

    return ""


def run(
    parametros=None,
    chat=None,
    silent=False
):

    parametros = parametros or []

    if not silent:

        console.print()

        console.rule(
            "[bold blue]VC TERM[/bold blue]"
        )

    try:

        payload = task_0200.get_keywords(
            silent=True
        )

    except Exception as e:

        if silent:

            return erro(
                str(e)
            )

        console.print(
            f"[red]Erro ao carregar palavras-chave:[/red] {e}"
        )

        return False

    keywords = extrair_keywords(payload)

    if len(keywords) == 0:

        resultado = {
            "success": True,
            "total_keywords": 0,
            "avaliados": 0,
            "inseridos": 0,
            "existentes": 0,
            "ignorados": 0,
            "tabela": "brapci_vc.vc_term"
        }

        if silent:

            return resultado

        console.print(
            "[yellow]Nenhuma palavra-chave encontrada para sincronizar.[/yellow]"
        )

        return resultado

    conexao = None
    cursor = None

    inseridos = 0
    existentes = 0
    ignorados = 0
    avaliados = 0
    vistos = set()

    try:

        conexao = conectar()
        cursor = conexao.cursor()

        for item in keywords:

            termo = normalizar_termo(
                item.get("name")
            )

            idioma = str(
                item.get("lang", "")
            ).strip().lower()

            if termo == "" or idioma == "":

                ignorados += 1
                continue

            chave = (
                termo.casefold(),
                idioma
            )

            if chave in vistos:

                ignorados += 1
                continue

            vistos.add(chave)
            avaliados += 1

            cursor.execute(
                """
                SELECT 1
                  FROM brapci_vc.vc_term
                 WHERE v_term = %s
                   AND v_lang = %s
                 LIMIT 1
                """,
                (
                    termo,
                    idioma
                )
            )

            if cursor.fetchone():

                existentes += 1
                continue

            cursor.execute(
                """
                INSERT INTO brapci_vc.vc_term (
                    v_term,
                    v_concept,
                    v_lang,
                    v_erro
                ) VALUES (%s, %s, %s, %s)
                """,
                (
                    termo,
                    0,
                    idioma,
                    montar_v_erro(termo)
                )
            )

            inseridos += 1

        conexao.commit()

    except Exception as e:

        if conexao is not None:

            conexao.rollback()

        if silent:

            return erro(
                str(e)
            )

        console.print(
            f"[red]Erro ao sincronizar vc_term:[/red] {e}"
        )

        return False

    finally:

        if cursor is not None:

            cursor.close()

        if conexao is not None:

            conexao.close()

    resultado = {
        "success": True,
        "total_keywords": len(keywords),
        "avaliados": avaliados,
        "inseridos": inseridos,
        "existentes": existentes,
        "ignorados": ignorados,
        "tabela": "brapci_vc.vc_term"
    }

    if silent:

        return resultado

    console.print(
        f"[bold green]✔ {inseridos} termos inseridos em brapci_vc.v_term.[/bold green]"
    )

    console.print(
        f"[cyan]Avaliados:[/cyan] {avaliados}"
    )

    console.print(
        f"[yellow]Já existentes:[/yellow] {existentes}"
    )

    console.print(
        f"[magenta]Ignorados:[/magenta] {ignorados}"
    )

    return resultado