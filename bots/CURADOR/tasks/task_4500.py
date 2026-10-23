#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Consulta a fila Z39.50 e envia os ISBNs pendentes ao endpoint."""

import requests
from rich.console import Console

from lib.database import conectar

console = Console()

TASK = {
    "id": 4500,
    "name": "Z3950 Queue",
    "description": "Consulta brapci.z3950_queue e envia os ISBNs pending à API.",
    "patterns": ["z3950 queue", "fila z3950"],
    "parameters": [],
}

URL = "https://cip.brapci.inf.br/api/find/z3950/queue"


def consultar_pendentes():
    conexao = conectar()
    try:
        cursor = conexao.cursor(dictionary=True)
        try:
            cursor.execute(
                "SELECT id, isbn FROM brapci.z3950_queue "
                "WHERE status = %s ORDER BY id",
                ("pending",),
            )
            return cursor.fetchall()
        finally:
            cursor.close()
    finally:
        conexao.close()


def run(parametros=None, chat=None, silent=False):
    try:
        pendentes = consultar_pendentes()
    except Exception as exc:
        resultado = {"success": False, "error": str(exc)}
        if not silent:
            console.print(resultado)
        return resultado

    enviados = 0
    erros = []
    with requests.Session() as session:
        for item in pendentes:
            try:
                isbn = str(item["isbn"] or "").strip()
                if not isbn:
                    raise ValueError("ISBN vazio na fila.")
                with session.post(
                    URL,
                    json={"isbn": isbn},
                    headers={"Content-Type": "application/json"},
                    timeout=30,
                ) as resposta:
                    resposta.raise_for_status()
                enviados += 1
            except (requests.RequestException, ValueError) as exc:
                erros.append({"id": item["id"], "isbn": item["isbn"], "error": str(exc)})

    # O endpoint é responsável pela fila; aceitar o POST não conclui a coleta.
    resultado = {
        "success": not erros,
        "total_pending": len(pendentes),
        "enviados": enviados,
        "falhas": len(erros),
        "erros": erros,
    }
    if not silent:
        console.print(resultado)
    return resultado
