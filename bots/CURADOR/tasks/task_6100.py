#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Task 6000
Consulta pesquisador no DATASET.
"""

import requests

from rich.console import Console
from rich.table import Table

console = Console()

TASK = {
    "id": 6100,
    "name": "DATASET",
    "description": "Exportar dados para Dataset.",
    "patterns": ["dataset"],
    "parameters": [{
        "name": "nome",
        "type": "string",
        "required": True
    }]
}

import json
import requests
import pymysql

URL = "https://cip.brapci.inf.br/api/brapci/get/v1/{}"

from pathlib import Path
import os

import pymysql
from dotenv import load_dotenv

# Localiza o .env na raiz do projeto
BASE_DIR = Path(__file__).resolve().parent.parent
load_dotenv(BASE_DIR / ".env")


def get_connection(database=None):
    """
    Retorna uma conexão MySQL utilizando as configurações do .env.

    Parameters
    ----------
    database : str | None
        Se informado, sobrescreve o banco definido no .env.
    """

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


def dataset_sync(id_work):
    """
    Atualiza ou insere um registro na tabela brapci_elastic.dataset.

    Parameters
    ----------
    id_work : int
        ID do trabalho na BRAPCI.
    conn : pymysql.Connection
        Conexão aberta com o MySQL.
    """


def keywords(lista):
    """
    Converte uma lista de palavras-chave em uma string.
    Aceita tanto lista de strings quanto lista de objetos.
    """

    resultado = []

    for item in lista:
        if isinstance(item, str):
            resultado.append(item)

        elif isinstance(item, dict):
            resultado.append(item.get("name", ""))

    return "; ".join(filter(None, resultado))

def dataset_sync(id_work):

    conn = get_connection("brapci_elastic")

    try:
        # Recupera os metadados
        r = requests.get(URL.format(id_work), timeout=60)
        r.raise_for_status()

        data = r.json()

        issue = data.get("Issue", {})
        subject = data.get("subject", {})

        # Autores
        authors = "; ".join(a["name"] for a in data.get("creator_author", [])
                            if "name" in a)

        # Palavras-chave
        kw_pt = keywords(subject.get("pt", []))
        kw_en = keywords(subject.get("en", []))
        kw_es = keywords(subject.get("es", []))
        kw_fr = keywords(subject.get("fr", []))

        sql = """
        INSERT INTO dataset
        (
            ID,
            DOI,
            CLASS,
            COLLECTION,
            json,
            JOURNAL,
            PUBLICATION,
            ISSUE,
            TITLE,
            AUTHORS,
            KEYWORDS,
            ABSTRACTS,
            SESSION,
            SESSION_SUB,
            LEGEND,
            YEAR,
            PDF,
            KEYWORDS_EN,
            KEYWORDS_ES,
            KEYWORDS_FR,
            URL,
            COVER,
            updated_at
        )
        VALUES
        (
            %(ID)s,
            %(DOI)s,
            %(CLASS)s,
            %(COLLECTION)s,
            %(JSON)s,
            %(JOURNAL)s,
            %(PUBLICATION)s,
            %(ISSUE)s,
            %(TITLE)s,
            %(AUTHORS)s,
            %(KEYWORDS)s,
            %(ABSTRACT)s,
            %(SESSION)s,
            %(SESSION_SUB)s,
            %(LEGEND)s,
            %(YEAR)s,
            %(PDF)s,
            %(KEYWORDS_EN)s,
            %(KEYWORDS_ES)s,
            %(KEYWORDS_FR)s,
            %(URL)s,
            %(COVER)s,
            NOW()
        )
        ON DUPLICATE KEY UPDATE

            DOI          = VALUES(DOI),
            CLASS        = VALUES(CLASS),
            COLLECTION   = VALUES(COLLECTION),
            json         = VALUES(json),
            JOURNAL      = VALUES(JOURNAL),
            PUBLICATION  = VALUES(PUBLICATION),
            ISSUE        = VALUES(ISSUE),
            TITLE        = VALUES(TITLE),
            AUTHORS      = VALUES(AUTHORS),
            KEYWORDS     = VALUES(KEYWORDS),
            ABSTRACTS    = VALUES(ABSTRACTS),
            SESSION      = VALUES(SESSION),
            SESSION_SUB  = VALUES(SESSION_SUB),
            LEGEND       = VALUES(LEGEND),
            YEAR         = VALUES(YEAR),
            PDF          = VALUES(PDF),
            KEYWORDS_EN  = VALUES(KEYWORDS_EN),
            KEYWORDS_ES  = VALUES(KEYWORDS_ES),
            KEYWORDS_FR  = VALUES(KEYWORDS_FR),
            URL          = VALUES(URL),
            COVER        = VALUES(COVER),
            updated_at   = NOW();
        """

        values = {
            "ID": int(data.get("ID", 0)),
            "DOI": "",
            "CLASS": data.get("Class", ""),
            "COLLECTION": "JA",
            "JSON": json.dumps(data, ensure_ascii=False),
            "JOURNAL": int(issue.get("jnl_rdf", 0) or 0),
            "PUBLICATION": issue.get("publisher", ""),
            "ISSUE": int(issue.get("ID", 0) or 0),
            "TITLE": data.get("title", ""),
            "AUTHORS": authors,
            "KEYWORDS": kw_pt,
            "ABSTRACT": data.get("description", ""),
            "SESSION": "",
            "SESSION_SUB": "",
            "LEGEND": data.get("legend", ""),
            "YEAR": int(data.get("year", 0) or 0),
            "PDF": 1 if data.get("resource_pdf") else 0,
            "KEYWORDS_EN": kw_en,
            "KEYWORDS_ES": kw_es,
            "KEYWORDS_FR": kw_fr,
            "URL": "",
            "COVER": data.get("cover", ""),
        }

        with conn.cursor() as cur:
            cur.execute(sql, values)

        conn.commit()
        with conn.cursor() as cur:
            cur.execute(sql, values)

        conn.commit()
        return values

    except Exception:
        conn.rollback()
        raise

    finally:
        conn.close()


def erro(mensagem):

    return {"success": False, "error": mensagem}


def buscar(nome, silent=False):
    """
    Pesquisa pesquisadores pelo nome.
    """

    if not silent:

        console.print(f"[bold blue]Consultando DATASET:[/bold blue] {nome}")

    r = requests.get(URL,
                     params={"q": f'"{nome}"'},
                     headers=HEADERS,
                     timeout=20)

    r.raise_for_status()

    return r.json()


def ultima_instituicao(item):
    """
    Retorna a última instituição do pesquisador.
    """

    instituicoes = item.get("institution-name")

    if instituicoes is None:

        return ""

    if isinstance(instituicoes, str):

        return instituicoes

    if isinstance(instituicoes, list):

        if len(instituicoes):

            return instituicoes[-1]

    return ""


def run(parametros=None, chat=None, silent=False):

    if parametros is None:

        parametros = []

    if not silent:

        console.print()

        console.rule("[bold blue]DATASET[/bold blue]")

    if len(parametros) == 0:

        if silent:

            return erro("Informe o nome do pesquisador.")

        console.print("[bold red]Informe o nome do pesquisador.[/bold red]")

        console.print()

        console.print("Exemplo:")

        console.print("    DATASET Rene Faustino Gabriel Junior")

        return False


    dataset_sync(parametros[0])

if __name__ == "__main__":
    run(parametros=["73005"], silent=False)
