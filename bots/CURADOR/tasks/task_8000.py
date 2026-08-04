#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import os
import sys
from pathlib import Path

import pymysql
from dotenv import load_dotenv

TASK = {
    "id": 8000,
    "name": "Citações",
    "description": "Citações de periódicos cadastrados na BRAPCI.",
    "patterns": [
        "coletar citações",
        "cited",
    ],
    "parameters": [
        {
            "name": "fonte",
            "type": "string",
            "required": False
        }
    ]
}


# Localiza o .env na raiz do CURADOR (bots/CURADOR/.env)
BASE_DIR = Path(__file__).resolve().parent.parent
load_dotenv(BASE_DIR / ".env")


def erro(mensagem):
    return {
        "success": False,
        "error": mensagem,
    }


def get_connection(database=None):
    return pymysql.connect(
        host=os.getenv("DB_HOST", "localhost"),
        port=int(os.getenv("DB_PORT", 3306)),
        user=os.getenv("DB_USERNAME"),
        password=os.getenv("DB_PASSWORD"),
        database=database or os.getenv("DB_DATABASE"),
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=False,
    )


def run(parametros=None, chat=None, silent=False):
    if parametros is None:
        parametros = []

    action = parametros[0].lower() if len(parametros) > 0 else "status"
    print(f"Acao: {action}")
    if action == "check":
        return check_01(silent=silent)

    if silent:
        return erro("Acao invalida. Use CHECK.")

    print("Acao invalida. Use CHECK.")
    return False


def check_01(silent=False):
    conn = None

    try:
        conn = get_connection("brapci_cited")

        with conn.cursor() as cur:
            cur.execute("SELECT COUNT(*) AS total FROM cited_article")
            total = int(cur.fetchone().get("total", 0))

            cur.execute(
                """
                UPDATE cited_article
                SET ca_text = REPLACE(REPLACE(ca_text, %s, %s), %s, %s)
                WHERE ca_text LIKE %s OR ca_text LIKE %s
                """,
                (
                    "\\_",
                    "_",
                    "/_",
                    "_",
                    "%\\_%",
                    "%/_%",
                ),
            )
            changed = cur.rowcount
            conn.commit()

        result = {
            "success": True,
            "table": "brapci_cited.cited_article",
            "total_rows": total,
            "updated_rows": int(changed),
            "message": "Substituicoes aplicadas em ca_text: \\_ -> _ e /_ -> _",
        }

        if silent:
            return result

        print("Check 01")
        print(f"Total de registros: {total}")
        print(f"Registros atualizados: {changed}")
        return None

    except Exception as e:
        if conn is not None:
            conn.rollback()

        if silent:
            return erro(str(e))

        print("Erro no check_01:", e)
        return False

    finally:
        if conn is not None:
            conn.close()

if __name__ == "__main__":
    run(parametros=sys.argv[2:], silent=False)
