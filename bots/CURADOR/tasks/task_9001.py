#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Task 9000
Envia um e-mail de teste utilizando SMTP.
"""

import os
import smtplib
from email.message import EmailMessage
from pathlib import Path

from dotenv import load_dotenv
from rich.console import Console

console = Console()

# ---------------------------------------------------------------------
# Carrega .env
# ---------------------------------------------------------------------

BASE_DIR = Path(__file__).resolve().parent.parent
load_dotenv(BASE_DIR / ".env")

TASK = {
    "id":
    9001,
    "name":
    "Teste de E-mail",
    "description":
    "Envia um e-mail de teste utilizando SMTP.",
    "patterns":
    ["mail", "email", "teste email", "teste e-mail", "smtp", "enviar email"],
    "parameters": [{
        "name": "email",
        "type": "string",
        "required": False
    }]
}

# ---------------------------------------------------------------------
# Utilidades
# ---------------------------------------------------------------------


def erro(msg):
    return {"success": False, "error": msg}


def ok(msg):
    return {"success": True, "message": msg}


# ---------------------------------------------------------------------
# MAIN
# ---------------------------------------------------------------------


def run(parametros=None, chat=None, silent=False):

    parametros = parametros or []

    # ------------------------------------------------------------
    # Destinatário
    # ------------------------------------------------------------

    if len(parametros):

        destino = parametros[0]

    else:

        destino = input("E-mail destino [renefgj@gmail.com]: ").strip()

        if destino == "":
            destino = "renefgj@gmail.com"

    # ------------------------------------------------------------
    # Configuração SMTP
    # ------------------------------------------------------------

    HOST = os.getenv("MAIL_HOST", "smtp.gmail.com")
    PORT = int(os.getenv("MAIL_PORT", "587"))
    USER = os.getenv("MAIL_USERNAME")
    PASSWORD = os.getenv("MAIL_PASSWORD")
    FROM = os.getenv("MAIL_FROM", USER)
    FROM_NAME = os.getenv("MAIL_FROM_NAME", "CURADOR")
    REPLY = os.getenv("MAIL_REPLY_TO", FROM)

    ENCRYPTION = os.getenv("MAIL_ENCRYPTION", "TLS").upper()

    TIMEOUT = int(os.getenv("MAIL_TIMEOUT", "30"))

    DEBUG = int(os.getenv("MAIL_DEBUG", "1"))

    if USER is None or PASSWORD is None:
        return erro("MAIL_USERNAME ou MAIL_PASSWORD não configurados.")

    # ------------------------------------------------------------
    # Monta mensagem
    # ------------------------------------------------------------

    msg = EmailMessage()

    msg["Subject"] = "Teste SMTP - CURADOR"

    msg["From"] = f"{FROM_NAME} <{FROM}>"

    msg["To"] = destino

    msg["Reply-To"] = REPLY

    msg.set_content("""Olá,

Este é um e-mail de teste enviado pelo CURADOR.

Se você recebeu esta mensagem, a configuração SMTP está funcionando corretamente.

--
BRAPCI CURADOR
""")

    try:

        if not silent:
            console.rule("[bold cyan]SMTP[/bold cyan]")

            console.print(f"Servidor : {HOST}")
            console.print(f"Porta    : {PORT}")
            console.print(f"Usuário  : {USER}")
            console.print(f"Destino  : {destino}")
            console.print()

        smtp = smtplib.SMTP(HOST, PORT, timeout=TIMEOUT)

        # Mostra toda negociação SMTP
        smtp.set_debuglevel(DEBUG)

        smtp.ehlo()

        if ENCRYPTION == "TLS":

            smtp.starttls()

            smtp.ehlo()

        smtp.login(USER, PASSWORD)

        smtp.send_message(msg)

        smtp.quit()

        if not silent:
            console.print()
            console.print(
                "[bold green]✓ E-mail enviado com sucesso.[/bold green]")

        return ok(f"E-mail enviado para {destino}")

    except Exception as e:

        if not silent:
            console.print_exception()

        return erro(str(e))
