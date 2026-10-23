#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Coleta e grava metadados dos ISBNs pendentes usando a consulta getISBN da API."""

import requests
import time
from contextlib import nullcontext
from rich.console import Console
from rich.live import Live
from rich.table import Table
from rich.console import Group
from rich.text import Text

from lib.database import conectar

console = Console()

TASK = {
    "id": 4500,
    "name": "Z3950 Queue",
    "description": "Coleta metadados dos ISBNs pending em brapci_z39.50.z3950_queue.",
    "patterns": ["z3950 queue", "fila z3950"],
    "parameters": [],
}

URL = "https://cip.brapci.inf.br/api/find/getISBN"


def consultar_pendentes():
    conexao = conectar()
    try:
        cursor = conexao.cursor(dictionary=True)
        try:
            cursor.execute(
                "SELECT id, isbn FROM `brapci_z39.50`.`z3950_queue` "
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
    fases = []

    def fase(nome, detalhe):
        fases.append((nome, detalhe))

    def painel(atual, isbn, status):
        tabela = Table(title="Coleta bibliográfica Z39.50")
        tabela.add_column("Progresso")
        tabela.add_column("ISBN")
        tabela.add_column("Situação")
        tabela.add_row(f"{atual}/{len(pendentes)}", isbn, status)
        tabela.caption = (
            f"Coletados: {coletados} | Não encontrados: {nao_encontrados} | Erros: {len(erros)}"
        )
        detalhes = Table(title="Endpoint e fases da consulta", expand=True)
        detalhes.add_column("Fase", width=20)
        detalhes.add_column("Detalhes", overflow="fold")
        detalhes.add_row("Endpoint", Text(f"GET {URL}"))
        detalhes.add_row("Query", Text(f"query={isbn}"))
        detalhes.add_row("Timeout", "90 segundos")
        for nome, detalhe in fases:
            detalhes.add_row(Text(nome), Text(detalhe))
        return Group(tabela, detalhes)

    display = nullcontext() if silent else Live(painel(0, "", "Iniciando"), console=console)
    with display as live, requests.Session() as session:
        for indice, item in enumerate(pendentes, 1):
            fases.clear()
            isbn = str(item["isbn"] or "").strip()
            fase("Preparação", "Validando ISBN e preparando requisição JSON")
            if live:
                live.update(painel(indice, isbn, "Consultando servidor Z39.50..."), refresh=True)
            try:
                if not isbn:
                    raise ValueError("ISBN vazio na fila.")
                fase("HTTP", "Enviando GET HTTPS; aguardando coleta e resposta do servidor")
                if live:
                    live.update(painel(indice, isbn, "Aguardando endpoint..."), refresh=True)
                inicio = time.monotonic()
                with session.get(
                    URL,
                    params={"query": isbn},
                    timeout=90,
                ) as resposta:
                    fase("Resposta HTTP", f"Status {resposta.status_code}; tempo {time.monotonic() - inicio:.2f}s")
                    fase("Formato", resposta.headers.get("Content-Type", "Não informado"))
                    if not resposta.ok:
                        fase("Erro HTTP", resposta.text[:1000])
                    resposta.raise_for_status()
                    job = resposta.json()
                if not isinstance(job, (dict, list)):
                    raise ValueError("Resposta inv?lida da API de coleta.")
                if isinstance(job, dict) and (job.get("error") or job.get("success") is False):
                    raise ValueError(str(job.get("error") or job.get("message") or "Falha na coleta."))
                if job:
                    coletados += 1
                    fase("Coleta", "Metadados retornados pelo getISBN")
                    situacao = "Metadados coletados"
                else:
                    nao_encontrados += 1
                    situacao = "ISBN n?o encontrado"
            except (requests.RequestException, ValueError) as exc:
                fase("Falha", str(exc))
                erros.append({"id": item["id"], "isbn": item["isbn"], "error": str(exc)})
                situacao = str(exc)
            if live:
                live.update(painel(indice, isbn, situacao), refresh=True)
        if live:
            live.update(painel(len(pendentes), isbn if pendentes else "", "Finalizado"), refresh=True)

    # getISBN retorna os metadados diretamente; resposta vazia indica aus?ncia de resultado.
    resultado = {
        "success": not erros,
        "total_pending": len(pendentes),
        "coletados": coletados,
        "nao_encontrados": nao_encontrados,
        "falhas": len(erros),
        "erros": erros,
    }
    return resultado if silent else None
