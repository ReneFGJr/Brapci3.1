#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Coleta e grava metadados dos ISBNs pendentes usando o worker da API."""

import requests
from contextlib import nullcontext
from rich.console import Console
from rich.live import Live
from rich.table import Table

from lib.database import conectar

console = Console()

TASK = {
    "id": 4500,
    "name": "Z3950 Queue",
    "description": "Coleta metadados dos ISBNs pending em brapci.z3950_queue.",
    "patterns": ["z3950 queue", "fila z3950"],
    "parameters": [],
}

URL = "https://cip.brapci.inf.br/api/find/z3950/queue/process"


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

    coletados = 0
    nao_encontrados = 0
    erros = []

    def painel(atual, isbn, status):
        tabela = Table(title="Coleta bibliográfica Z39.50")
        tabela.add_column("Progresso")
        tabela.add_column("ISBN")
        tabela.add_column("Situação")
        tabela.add_row(f"{atual}/{len(pendentes)}", isbn, status)
        tabela.caption = (
            f"Coletados: {coletados} | Não encontrados: {nao_encontrados} | Erros: {len(erros)}"
        )
        return tabela

    display = nullcontext() if silent else Live(painel(0, "", "Iniciando"), console=console)
    with display as live, requests.Session() as session:
        for indice, item in enumerate(pendentes, 1):
            isbn = str(item["isbn"] or "").strip()
            if live:
                live.update(painel(indice, isbn, "Consultando servidor Z39.50..."), refresh=True)
            try:
                if not isbn:
                    raise ValueError("ISBN vazio na fila.")
                with session.post(
                    URL,
                    json={"isbn": isbn},
                    headers={"Content-Type": "application/json"},
                    timeout=90,
                ) as resposta:
                    resposta.raise_for_status()
                    job = resposta.json()
                if not isinstance(job, dict):
                    raise ValueError("Resposta inválida da API de coleta.")
                status = job.get("status")
                if status == "completed" and job.get("result"):
                    coletados += 1
                    situacao = "Metadados coletados e gravados"
                elif status == "not_found":
                    nao_encontrados += 1
                    situacao = "ISBN não encontrado"
                else:
                    raise ValueError(job.get("last_error") or f"Coleta não concluída: {status}")
            except (requests.RequestException, ValueError) as exc:
                erros.append({"id": item["id"], "isbn": item["isbn"], "error": str(exc)})
                situacao = str(exc)
            if live:
                live.update(painel(indice, isbn, situacao), refresh=True)
        if live:
            live.update(painel(len(pendentes), "", "Finalizado"), refresh=True)

    # Somente completed com resultado representa metadados coletados.
    resultado = {
        "success": not erros,
        "total_pending": len(pendentes),
        "coletados": coletados,
        "nao_encontrados": nao_encontrados,
        "falhas": len(erros),
        "erros": erros,
    }
    return resultado if silent else None
