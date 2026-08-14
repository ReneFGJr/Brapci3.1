import requests
import csv
import time

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
    "parameters": [{
        "name": "fonte",
        "type": "string",
        "required": False
    }]
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

ARQUIVO_ENTRADA = "dois_id.txt"
ARQUIVO_SAIDA = "resultado_dois.csv"

HEADERS = {"User-Agent": "DOI-Checker/1.0 (mailto:seu-email@exemplo.com)"}


def limpar_doi(doi):
    """Remove URL, espaços e prefixos comuns."""
    doi = doi.strip()

    doi = doi.replace("https://doi.org/", "")
    doi = doi.replace("http://doi.org/", "")
    doi = doi.replace("http://dx.doi.org/", "")
    doi = doi.replace("doi:", "")
    doi = doi.replace("DOI:", "")

    return doi.strip()


def verificar_crossref(doi):
    """Verifica se o DOI está registrado na Crossref."""

    url = f"https://api.crossref.org/works/{doi}"

    try:
        response = requests.get(url, headers=HEADERS, timeout=15)

        if response.status_code == 200:
            dados = response.json()["message"]

            return {
                "encontrado":
                True,
                "titulo":
                (dados.get("title", [""])[0] if dados.get("title") else ""),
                "publisher":
                dados.get("publisher", "")
            }

        if response.status_code == 404:
            return {"encontrado": False}

        return {"encontrado": False, "erro": f"HTTP {response.status_code}"}

    except requests.RequestException as e:
        return {"encontrado": False, "erro": str(e)}


def verificar_datacite(doi):
    """Verifica se o DOI está registrado na DataCite."""

    url = f"https://api.datacite.org/dois/{doi}"

    try:
        response = requests.get(url, headers=HEADERS, timeout=15)

        if response.status_code == 200:
            dados = response.json()["data"]["attributes"]

            titulo = ""

            if dados.get("titles"):
                titulo = dados["titles"][0].get("title", "")

            return {
                "encontrado": True,
                "titulo": titulo,
                "publisher": dados.get("publisher", "")
            }

        if response.status_code == 404:
            return {"encontrado": False}

        return {"encontrado": False, "erro": f"HTTP {response.status_code}"}

    except requests.RequestException as e:
        return {"encontrado": False, "erro": str(e)}


def verificar_doi(doi):

    # Primeiro Crossref
    crossref = verificar_crossref(doi)

    if crossref.get("encontrado"):
        return {
            "doi": doi,
            "registrado": "SIM",
            "agencia": "Crossref",
            "titulo": crossref.get("titulo", ""),
            "publisher": crossref.get("publisher", "")
        }

    # Depois DataCite
    datacite = verificar_datacite(doi)

    if datacite.get("encontrado"):
        return {
            "doi": doi,
            "registrado": "SIM",
            "agencia": "DataCite",
            "titulo": datacite.get("titulo", ""),
            "publisher": datacite.get("publisher", "")
        }

    return {
        "doi": doi,
        "registrado": "NÃO",
        "agencia": "",
        "titulo": "",
        "publisher": ""
    }

def check_doi():
    # --------------------------------------------------
    # Ler arquivo
    # --------------------------------------------------

    with open(ARQUIVO_ENTRADA, "r", encoding="utf-8") as arquivo:
        dois = [limpar_doi(linha) for linha in arquivo if linha.strip()]

    dois_consultados = set()
    if os.path.exists(ARQUIVO_SAIDA):
        with open(ARQUIVO_SAIDA, "r", newline="", encoding="utf-8-sig") as arquivo:
            reader = csv.DictReader(arquivo, delimiter=";")
            dois_consultados = {
                limpar_doi(linha["doi"])
                for linha in reader
                if linha.get("doi")
            }

    dois_novos = [doi for doi in dois if doi not in dois_consultados]

    print(f"Total de DOIs: {len(dois)}")
    print(f"DOIs ja consultados: {len(dois) - len(dois_novos)}")
    print(f"DOIs pendentes: {len(dois_novos)}")

    # --------------------------------------------------
    # Verificar
    # --------------------------------------------------

    resultados = []

    for numero, doi in enumerate(dois_novos, start=1):

        print(f"[{numero}/{len(dois_novos)}] Verificando {doi}", end=" ... ")

        resultado = verificar_doi(doi)

        resultados.append(resultado)

        print(resultado["registrado"], resultado["agencia"])

        # Pequena pausa para não sobrecarregar as APIs
        time.sleep(0.1)

    # --------------------------------------------------
    # Salvar CSV
    # --------------------------------------------------

    arquivo_existe = os.path.exists(ARQUIVO_SAIDA)
    with open(ARQUIVO_SAIDA, "a", newline="", encoding="utf-8-sig") as arquivo:

        campos = ["doi", "registrado", "agencia", "titulo", "publisher"]

        writer = csv.DictWriter(arquivo, fieldnames=campos, delimiter=";")

        if not arquivo_existe:
            writer.writeheader()
        writer.writerows(resultados)

print()
print(f"Resultado salvo em: {ARQUIVO_SAIDA}")

def createDOIfile():
    COLLECTION = "JA"
    YEAR = "2025"
    conn = None

    try:
        conn = get_connection("brapci_elastic")

        with conn.cursor() as cur:
            cur.execute(
                """
                SELECT DOI
                FROM brapci_elastic.dataset
                WHERE COLLECTION = %s
                  AND YEAR = %s
                  AND DOI IS NOT NULL
                  AND TRIM(DOI) <> ''
                ORDER BY ID
                """,
                (COLLECTION, YEAR),
            )
            dois = [row["DOI"].strip() for row in cur.fetchall()]

        with open(ARQUIVO_ENTRADA, "w", encoding="utf-8") as arquivo:
            for doi in dois:
                arquivo.write(f"{doi}\n")

        print(f"Total de DOIs salvos: {len(dois)}")
        print(f"Arquivo salvo em: {ARQUIVO_ENTRADA}")
        return dois

    except Exception as e:
        print("Erro no createDOIfile:", e)
        return erro(str(e))

    finally:
        if conn is not None:
            conn.close()

def run(parametros=None, chat=None, silent=False):
    if parametros is None:
        parametros = []

    action = parametros[0].lower() if len(parametros) > 0 else "status"
    print(f"Acao: {action}")

    if action == 'made':
        return createDOIfile()

    if action == "check":
        check_doi()

if __name__ == "__main__":
    run(parametros=sys.argv[2:], silent=False)
