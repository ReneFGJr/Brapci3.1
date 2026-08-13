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


import re


def recuperar_ano(texto: str) -> int:
    """
    Recupera o ano de uma referência bibliográfica.

    Retornos:
        YYYY -> ano identificado
        9999 -> nenhum ano identificado
        -2   -> mais de um ano distinto identificado
    """

    if not texto:
        return 9999

    anos = set()

    # ---------------------------------------------------------
    # 1. Anos explícitos com quatro dígitos
    #    Ex.: 1991, 2025
    # ---------------------------------------------------------
    encontrados = re.findall(r'\b(18\d{2}|19\d{2}|20\d{2}|21\d{2})\b', texto)

    for ano in encontrados:
        anos.add(int(ano))

    # ---------------------------------------------------------
    # 2. Datas no formato DD/MM/AA
    #    Ex.: [15/05/98] -> 1998
    # ---------------------------------------------------------
    datas_abreviadas = re.findall(r'\b\d{1,2}/\d{1,2}/(\d{2})\b', texto)

    for ano_curto in datas_abreviadas:
        ano = int(ano_curto)

        # Referências bibliográficas antigas:
        # 00-29 -> 2000-2029
        # 30-99 -> 1930-1999
        if ano <= 29:
            ano += 2000
        else:
            ano += 1900

        anos.add(ano)

    # ---------------------------------------------------------
    # 3. Avaliação do resultado
    # ---------------------------------------------------------

    if len(anos) == 0:
        return 9999

    if len(anos) > 1:
        return -2

    return anos.pop()

def run(parametros=None, chat=None, silent=False):
    if parametros is None:
        parametros = []

    action = parametros[0].lower() if len(parametros) > 0 else "status"
    print(f"Acao: {action}")
    if action == "check":
        result_01 = check_01(silent=silent)
        result_02 = check_02(silent=silent)
        result_03 = check_03(silent=silent)
        result_04 = check_04(silent=silent)

        if silent:
            return {
                "success": False,
                "checks": [result_01, result_02, result_03, result_04],
            }
        return result_02

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

            cur.execute(
                """
                UPDATE brapci_cited.cited_article
                SET
                    ca_text = REPLACE(ca_text,'-', '_'),
                    ca_year = 0
                WHERE ca_text LIKE '%---%';
                """
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

def check_02(silent=False):
    conn = None
    total = 0
    changed = 0

    try:
        conn = get_connection("brapci_cited")

        with conn.cursor() as cur:
            cur.execute("SELECT COUNT(*) AS total FROM cited_article")
            total = int(cur.fetchone().get("total", 0))

            cur.execute("""
                UPDATE brapci_cited.cited_article
                SET
                    ca_text = TRIM(SUBSTRING(ca_text, 2)),
                    ca_year = 0
                WHERE ca_text LIKE '-%' or ca_text LIKE '–%' or ca_text LIKE '—%' or ca_text LIKE '|%' or ca_text LIKE '°%';
                """)

            cur.execute("""
                UPDATE brapci_cited.cited_article
                SET
                    ca_text = TRIM(SUBSTRING(ca_text, 2)),
                    ca_year = 0
                    WHERE ca_text LIKE '1%'
                    or ca_text LIKE '2%' or ca_text LIKE '3%' or ca_text LIKE '4%'
                    or ca_text LIKE '5%' or ca_text LIKE '6%' or ca_text LIKE '7%'
                    or ca_text LIKE '8%' or ca_text LIKE '9%' or ca_text LIKE '0%';
                """)

            cur.execute("""
                UPDATE brapci_cited.cited_article
                SET
                    ca_text = TRIM(SUBSTRING(ca_text, 5)),
                    ca_year = 0
                WHERE ca_text LIKE '&lt;%';
                """)

            changed = cur.rowcount
            conn.commit()

        result = {
            "success": True,
            "table": "brapci_cited.cited_article",
            "total_rows": total,
            "updated_rows": int(changed),
            "message": "Hifen inicial removido de ca_text e ca_year definido como 0",
        }

        if silent:
            return result

        print("Check 02")
        print(f"Total de registros: {total}")
        print(f"Registros atualizados: {changed}")
        return None

    except Exception as e:
        if conn is not None:
            conn.rollback()

        if silent:
            return erro(str(e))

        print("Erro no check_02:", e)
        return False

    finally:
        if conn is not None:
            conn.close()

def check_03(silent=False):
    conn = None

    try:
        conn = get_connection("brapci_cited")

        with conn.cursor() as cur:
            cur.execute(
                """
                SELECT id_ca, ca_text
                FROM brapci_cited.cited_article
                WHERE ca_text LIKE 'http%'
                ORDER BY id_ca
                """
            )
            rows = cur.fetchall()

        # Para cada URL encontrada, inclui também o id_ca anterior mais próximo.
        ids_to_delete = set()
        with conn.cursor() as cur:
            for row in rows:
                current_id = int(row["id_ca"])
                ca_text = row["ca_text"]
                ids_to_delete.add(current_id)

                cur.execute(
                    """
                    SELECT MAX(id_ca) AS previous_id
                    FROM brapci_cited.cited_article
                    WHERE id_ca < %s
                    """,
                    (current_id,),
                )
                previous = cur.fetchone()
                if previous and previous.get("previous_id") is not None:
                    print("Adicionando id_ca anterior:", previous)
                    cur.execute(
                        """
                        UPDATE brapci_cited.cited_article
                        SET ca_text = CONCAT(ca_text, ' ', %s)
                        WHERE id_ca = %s
                        """,
                        (ca_text, previous["previous_id"],),
                    )

            deleted_ids = sorted(ids_to_delete)
            if deleted_ids:
                placeholders = ", ".join(["%s"] * len(deleted_ids))
                cur.execute(
                    f"DELETE FROM brapci_cited.cited_article WHERE id_ca IN ({placeholders})",
                    tuple(deleted_ids),
                )
                deleted_rows = cur.rowcount
            else:
                deleted_rows = 0

        conn.commit()

        result = {
            "success": True,
            "table": "brapci_cited.cited_article",
            "total_rows": len(rows),
            "rows": rows,
            "deleted_ids": deleted_ids,
            "deleted_rows": int(deleted_rows),
            "message": "Referencias iniciadas por http e seus registros anteriores removidos.",
        }

        if silent:
            return result

        print("Check 03")
        print(f"Total de registros encontrados: {len(rows)}")
        for row in rows:
            print(f"{row['id_ca']}: {row['ca_text']}")
        print(f"IDs removidos: {deleted_ids}")
        print(f"Total de registros removidos: {deleted_rows}")
        return None

    except Exception as e:
        if conn is not None:
            conn.rollback()

        if silent:
            return erro(str(e))

        print("Erro no check_03:", e)
        return False

    finally:
        if conn is not None:
            conn.close()

def check_04(silent=False):
    conn = None
    updated_rows = 0

    try:
        conn = get_connection("brapci_cited")

        with conn.cursor() as cur:
            cur.execute(
                """
                SELECT id_ca, ca_text
                FROM brapci_cited.cited_article
                WHERE ca_year = 0
                ORDER BY id_ca
                """
            )
            rows = cur.fetchall()

            for row in rows:
                id_ca = row["id_ca"]
                ca_text = row["ca_text"]
                ano = recuperar_ano(ca_text)

                cur.execute(
                    """
                    UPDATE brapci_cited.cited_article
                    SET ca_year = %s
                    WHERE id_ca = %s
                    """,
                    (ano, id_ca),
                )
                updated_rows += cur.rowcount

            conn.commit()

    except Exception as e:
        if conn is not None:
            conn.rollback()

        if silent:
            return erro(str(e))

        print("Erro no check_04:", e)
        return False
    finally:
        if conn is not None:
            conn.close()

    return {
        "success": True,
        "table": "brapci_cited.cited_article",
        "total_rows": len(rows),
        "updated_rows": updated_rows,
        "rows": rows,
        "message": "Ano recuperado de ca_text e atualizado em ca_year.",
    }

if __name__ == "__main__":
    run(parametros=sys.argv[2:], silent=False)
