#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Task 8002: atualiza as estatisticas exibidas pela BRAPCI."""

import os
import sys
from pathlib import Path

import pymysql
from dotenv import load_dotenv


TASK = {
    "id": 8002,
    "name": "Estatisticas",
    "description": "Atualiza as estatisticas de itens da BRAPCI.",
    "patterns": [
        "atualizar estatisticas",
        "estatisticas",
    ],
    "parameters": [],
}


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
        database=database or os.getenv("DB_DATABASE", "brapci"),
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=False,
    )


STATISTICS_QUERIES = (
    """
    SELECT COUNT(*) AS total, CLASS AS c_class
    FROM brapci_elastic.dataset
    GROUP BY CLASS
    """,
    """
    SELECT COUNT(*) AS total, c_class
    FROM brapci_rdf.rdf_concept
    INNER JOIN brapci_rdf.rdf_class ON cc_class = id_c
    WHERE cc_use = id_cc
      AND id_c IN (9, 13, 14, 18, 50)
    GROUP BY c_class, id_c
    ORDER BY id_c, c_class ASC
    """,
    """
    SELECT
        COUNT(*) AS total,
        CONCAT('collection', '_', jnl_collection) AS c_class
    FROM brapci.source_source
    WHERE jnl_collection NOT IN ('XX', 'BK')
    GROUP BY jnl_collection
    """,
    """
    SELECT
        COUNT(*) AS total,
        CONCAT('historic', '_', jnl_collection) AS c_class
    FROM brapci.source_source
    WHERE jnl_historic = 1
      AND jnl_collection NOT IN ('XX', 'BK')
    GROUP BY jnl_collection
    """,
)


def get_statistics(silent=False):
    """Calcula e substitui as estatisticas ITEM_* em uma transacao."""
    connection = None

    try:
        connection = get_connection("brapci")

        with connection.cursor() as cursor:
            rows = []
            for query in STATISTICS_QUERIES:
                cursor.execute(query)
                rows.extend(cursor.fetchall())

            if not rows:
                return erro("Nenhuma estatistica encontrada.")

            cursor.execute(
                "DELETE FROM brapci.statistics WHERE ind_name LIKE %s",
                ("ITEM_%",),
            )

            values = [
                (f"ITEM_{row['c_class']}", int(row["total"]))
                for row in rows
            ]
            cursor.executemany(
                """
                INSERT INTO brapci.statistics (ind_name, ind_total)
                VALUES (%s, %s)
                """,
                values,
            )

        connection.commit()

        result = {
            "success": True,
            "inserted": len(values),
            "message": "Atualizacao de estatisticas concluida.",
        }

        if not silent:
            for ind_name, ind_total in values:
                print(
                    f"Registro inserido: ind_name={ind_name}, "
                    f"ind_total={ind_total}"
                )
            print(result["message"])

        return result

    except Exception as exception:
        if connection is not None:
            connection.rollback()
        return erro(str(exception))

    finally:
        if connection is not None:
            connection.close()


def run(parametros=None, chat=None, silent=False):
    parametros = parametros or []
    action = parametros[0].lower() if parametros else "update"

    if action in ("update", "atualizar", "make"):
        return get_statistics(silent=silent)

    if action == "status" or action == '':
        return {
            "success": True,
            "task": TASK["name"],
            "status": "ready",
        }

    return erro("Acao desconhecida. Use UPDATE ou STATUS.")


if __name__ == "__main__":
    resultado = run(parametros=sys.argv[1:], silent=False)
    if not resultado.get("success", False):
        print(resultado)
