#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Executa um ciclo do exportador legado da BRAPCI."""

import html
import os
import re
import time

import requests
from dotenv import load_dotenv
from pathlib import Path


DEFAULT_URL = "https://cip.brapci.inf.br/bots/export"

TASK = {
    "id": 2001,
    "name": "Executar exportação legada",
    "description": (
        "Executa um ciclo equivalente ao acesso de /bots/export no projeto "
        "Brapci3.1."
    ),
    "patterns": [
        "executar exportação legada",
        "exportar brapci",
        "bots export",
        "rodar exportador",
    ],
    "parameters": [],
}


def _text_from_html(content):
    """Produz um resumo textual curto da resposta HTML do endpoint."""
    content = re.sub(r"<(script|style)\b[^>]*>.*?</\1>", " ", content,
                     flags=re.IGNORECASE | re.DOTALL)
    content = re.sub(r"<[^>]+>", " ", content)
    content = html.unescape(content)
    return " ".join(content.split())


def export(url=None, timeout=None):
    """Acessa o endpoint uma vez, como faria o navegador/cron legado."""
    load_dotenv(Path(__file__).resolve().parent.parent / ".env")
    endpoint = (url or os.getenv("BRAPCI_EXPORT_URL") or DEFAULT_URL).strip()
    request_timeout = int(timeout or os.getenv("TIMEOUT", 300))
    started_at = time.monotonic()

    try:
        response = requests.get(
            endpoint,
            timeout=request_timeout,
            headers={"User-Agent": "CURADOR/2001"},
        )
        elapsed = round(time.monotonic() - started_at, 3)
        response.raise_for_status()
    except requests.RequestException as error:
        return {
            "success": False,
            "url": endpoint,
            "elapsed_seconds": round(time.monotonic() - started_at, 3),
            "error": str(error),
        }

    content_type = response.headers.get("Content-Type", "")
    body = response.text
    summary = _text_from_html(body)

    return {
        "success": True,
        "url": response.url,
        "status_code": response.status_code,
        "content_type": content_type,
        "elapsed_seconds": elapsed,
        "continue": "<CONTINUE>" in body or bool(
            re.search(r"http-equiv=[\"']?refresh", body, re.IGNORECASE)
        ),
        "message": summary[:1000],
    }


def run(parametros=None, chat=None, silent=False):
    parametros = parametros or []
    if parametros:
        return {
            "success": False,
            "error": "Uso: 2001",
        }

    result = export()
    if not silent:
        if result["success"]:
            print(result.get("message") or "Ciclo de exportação concluído.")
        else:
            print(f"Erro na exportação: {result['error']}")
    return result
