#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Task 6100
Exporta trabalho(s) para brapci_elastic.dataset a partir do ID.
"""

from datetime import datetime

import requests
from rich.console import Console
from rich.table import Table

import os
import json
import pymysql

from dotenv import load_dotenv
from lib.database import conectar

console = Console()

load_dotenv()

TASK = {
    "id": 6100,
    "name": "DATASET BY ID",
    "description":
    "Exporta metadados de um trabalho para a tabela brapci_elastic.dataset.",
    "patterns": ["dataset", "dataset id", "export dataset", "reindex id"],
    "parameters": [{
        "name": "id",
        "type": "integer",
        "required": True
    }]
}

URL_GET = "https://cip.brapci.inf.br/api/brapci/get/v2/{id}"
TIMEOUT = 30


def erro(mensagem):

    return {"success": False, "error": mensagem}


def parse_ids(parametros):

    ids = []

    bruto = " ".join(parametros)

    for parte in bruto.replace(",", " ").split():

        if str(parte).isdigit():

            ids.append(int(parte))

    vistos = set()
    limpos = []

    for item in ids:

        if item in vistos:

            continue

        vistos.add(item)
        limpos.append(item)

    return limpos


def texto_primeiro(valor):

    if isinstance(valor, list):

        for item in valor:

            txt = texto_primeiro(item)

            if txt != "":

                return txt

        return ""

    if isinstance(valor, dict):

        for chave in ["value", "text", "name", "title"]:

            if chave in valor:

                txt = texto_primeiro(valor.get(chave))

                if txt != "":

                    return txt

        return ""

    if valor is None:

        return ""

    return str(valor).strip()


def juntar_campos_multilingue(campo):

    itens = []

    if isinstance(campo, dict):

        for _, valores in campo.items():

            if isinstance(valores, list):

                for valor in valores:

                    txt = texto_primeiro(valor)

                    if txt != "":

                        itens.append(txt)

            else:

                txt = texto_primeiro(valores)

                if txt != "":

                    itens.append(txt)

    elif isinstance(campo, list):

        for valor in campo:

            txt = texto_primeiro(valor)

            if txt != "":

                itens.append(txt)

    else:

        txt = texto_primeiro(campo)

        if txt != "":

            itens.append(txt)

    vistos = set()
    unicos = []

    for item in itens:

        chave = item.casefold()

        if chave in vistos:

            continue

        vistos.add(chave)
        unicos.append(item)

    return "; ".join(unicos)


def autores_para_string(dados):

    autores = dados.get("Authors", [])

    itens = []

    if isinstance(autores, dict):

        for _, valor in autores.items():

            if isinstance(valor, list):

                for nome in valor:

                    txt = texto_primeiro(nome)

                    if txt != "":

                        itens.append(txt)

            else:

                txt = texto_primeiro(valor)

                if txt != "":

                    itens.append(txt)

    elif isinstance(autores, list):

        for nome in autores:

            txt = texto_primeiro(nome)

            if txt != "":

                itens.append(txt)

    organizadores = dados.get("Organizer", [])

    if isinstance(organizadores, list):

        for nome in organizadores:

            txt = texto_primeiro(nome)

            if txt != "":

                itens.append(f"{txt} (Org.)")

    vistos = set()
    unicos = []

    for item in itens:

        chave = item.casefold()

        if chave in vistos:

            continue

        vistos.add(chave)
        unicos.append(item)

    return "; ".join(unicos)


def titulo_preferencial(dados):

    tit = dados.get("Title", {})

    if isinstance(tit, dict):

        if "pt" in tit and len(tit["pt"]) > 0:

            txt = texto_primeiro(tit["pt"][0])

            if txt != "":

                return txt

        for _, valores in tit.items():

            if isinstance(valores, list) and len(valores) > 0:

                txt = texto_primeiro(valores[0])

                if txt != "":

                    return txt

    return ":: Sem titulo ::"


def extrair_doi_e_url(dados, idr):

    doi = texto_primeiro(dados.get("DOI", ""))

    if doi != "":

        if doi.lower().startswith("http"):

            return doi, doi

        return doi, f"https://doi.org/{doi.lstrip('/')}"

    return "", f"https://hdl.handle.net/20.500.11959/brapci/{idr}"


def montar_legend(class_name, issue, titulo):

    class_name = str(class_name or "")

    if class_name == "Article":

        journal = str(issue.get("journal", "")).strip()
        vol = str(issue.get("vol", "")).strip()
        nr = str(issue.get("nr", "")).strip()
        year = str(issue.get("year", "")).strip()

        legenda = journal

        if vol != "":

            legenda += f", v. {vol}"

        if nr != "":

            legenda += f", n. {nr}"

        if year != "":

            legenda += f", {year}"

        return legenda.strip(", ")

    if class_name == "Proceeding":

        legenda = titulo
        year = str(issue.get("year", "")).strip()

        if year != "":

            legenda += f", {year}"

        return legenda

    if class_name == "Book":

        year = str(issue.get("year", "")).strip()

        if year != "":

            return f"{titulo}, {year}"

        return titulo

    if class_name == "BookChapter":

        year = str(issue.get("year", "")).strip()
        base = f"Capítulo de livro - {titulo}"

        if year != "":

            return f"{base}, {year}"

        return base

    return titulo


def classe_para_collection(classe):

    classe = str(classe or "")

    if classe == "Book":

        return "BK"

    if classe == "BookChapter":

        return "BC"

    if classe == "Proceeding":

        return "EV"

    return ""


def mapear_dataset(dados):

    idr = int(dados.get("ID", 0))
    classe = str(dados.get("Class", "")).strip()
    issue = dados.get("Issue", {})

    if not isinstance(issue, dict):

        issue = {}

    titulo = titulo_preferencial(dados)
    doi, url = extrair_doi_e_url(dados, idr)

    keywords_txt = juntar_campos_multilingue(dados.get("Subject", {}))
    abstracts_txt = juntar_campos_multilingue(dados.get("Abstract", {}))

    year = str(dados.get("YEAR", "")).strip()

    if year == "":

        year = str(issue.get("year", "")).strip()

    if year == "":

        year = "2000"

    journal = dados.get("JOURNAL", "")

    if str(journal).strip() == "":

        journal = issue.get("id_jnl", 0)

    legend = montar_legend(classe, issue, titulo)

    publication = str(dados.get("PUBLICATION", "")).strip()

    if publication == "":

        publication = str(issue.get("journal", "")).strip()

    if classe == "Book":

        publication = "LIVRO"
        journal = 0

    if classe == "BookChapter":

        publication = "CAPITULO DE LIVRO"
        journal = 0

    oai_id = texto_primeiro(dados.get("Identifier", ""))

    if oai_id == "":

        oai_id = texto_primeiro(dados.get("OAI_ID", ""))

    dta = {
        "ID":
        idr,
        "json":
        dados,
        "CLASS":
        classe,
        "COVER":
        "/img/books/no_cover.png",
        "COLLECTION":
        str(dados.get("COLLECTION", "")).strip()
        or classe_para_collection(classe),
        "JOURNAL":
        int(journal) if str(journal).isdigit() else 0,
        "ISSUE":
        int(issue.get("issue", 0))
        if str(issue.get("issue", "")).isdigit() else 0,
        "YEAR":
        year,
        "KEYWORD":
        1 if keywords_txt != "" else 0,
        "ABSTRACT":
        1 if abstracts_txt != "" else 0,
        "KEYWORDS":
        keywords_txt,
        "ABSTRACTS":
        abstracts_txt,
        "PDF":
        int(dados.get("PDF", 0))
        if str(dados.get("PDF", "0")).isdigit() else 0,
        "status":
        1,
        "AUTHORS":
        autores_para_string(dados),
        "TITLE":
        titulo,
        "SESSION":
        juntar_campos_multilingue(dados.get("Sections", {})),
        "PUBLICATION":
        publication,
        "LEGEND":
        legend,
        "new":
        1,
        "use":
        0,
        "URL":
        url,
        "OAI_ID":
        oai_id,
        "DOI":
        doi,
        "updated_at":
        datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }

    return dta


def buscar_metadados(idr, silent=False):

    if not silent:

        console.print(f"[bold blue]Consultando metadados:[/bold blue] {idr}")

    url = URL_GET.format(id=idr)

    resposta = requests.get(url, timeout=TIMEOUT)

    resposta.raise_for_status()

    dados = resposta.json()

    if not isinstance(dados, dict):

        raise ValueError("API retornou payload inválido para o ID informado.")

    if str(dados.get("status", "200")) == "404":

        raise ValueError("Registro não encontrado na API BRAPCI.")

    dados["ID"] = int(idr)

    return dados


def upsert_dataset(cursor, dados):
    payload_json = json.dumps(dados["json"], ensure_ascii=False)

    sql = """
    INSERT INTO brapci_elastic.dataset (
        ID,json,CLASS,COVER,COLLECTION,JOURNAL,ISSUE,YEAR,
        KEYWORD,ABSTRACT,KEYWORDS,ABSTRACTS,PDF,status,
        AUTHORS,TITLE,SESSION,PUBLICATION,LEGEND,new,`use`,
        URL,OAI_ID,DOI,updated_at
    ) VALUES (
        %(ID)s,%(json)s,%(CLASS)s,%(COVER)s,%(COLLECTION)s,%(JOURNAL)s,
        %(ISSUE)s,%(YEAR)s,%(KEYWORD)s,%(ABSTRACT)s,%(KEYWORDS)s,
        %(ABSTRACTS)s,%(PDF)s,%(status)s,%(AUTHORS)s,%(TITLE)s,
        %(SESSION)s,%(PUBLICATION)s,%(LEGEND)s,%(new)s,%(use)s,
        %(URL)s,%(OAI_ID)s,%(DOI)s,NOW()
    )
    ON DUPLICATE KEY UPDATE
        json=VALUES(json),
        CLASS=VALUES(CLASS),
        COVER=VALUES(COVER),
        COLLECTION=VALUES(COLLECTION),
        JOURNAL=VALUES(JOURNAL),
        ISSUE=VALUES(ISSUE),
        YEAR=VALUES(YEAR),
        KEYWORD=VALUES(KEYWORD),
        ABSTRACT=VALUES(ABSTRACT),
        KEYWORDS=VALUES(KEYWORDS),
        ABSTRACTS=VALUES(ABSTRACTS),
        PDF=VALUES(PDF),
        status=VALUES(status),
        AUTHORS=VALUES(AUTHORS),
        TITLE=VALUES(TITLE),
        SESSION=VALUES(SESSION),
        PUBLICATION=VALUES(PUBLICATION),
        LEGEND=VALUES(LEGEND),
        new=VALUES(new),
        `use`=VALUES(`use`),
        URL=VALUES(URL),
        OAI_ID=VALUES(OAI_ID),
        DOI=VALUES(DOI),
        updated_at=NOW()
    """
    parametros = dados.copy()
    parametros["json"] = payload_json
    cursor.execute(sql, parametros)
    return "inserted" if cursor.rowcount == 1 else "updated"


def run(parametros=None, chat=None, silent=False):

    parametros = parametros or []

    if not silent:

        console.print()
        console.rule("[bold blue]DATASET BY ID[/bold blue]")

    ids = parse_ids(parametros)

    if len(ids) == 0:

        if silent:

            return erro("Informe ao menos um ID de trabalho.")

        console.print(
            "[bold red]Informe ao menos um ID de trabalho.[/bold red]")
        console.print()
        console.print("Exemplo:")
        console.print("    6100 123456")

        return False

    conexao = None
    cursor = None

    inseridos = 0
    atualizados = 0
    falhas = []

    try:

        conexao = conectar()
        cursor = conexao.cursor()

        for idr in ids:

            try:

                meta = buscar_metadados(idr, silent=silent)

                dataset = mapear_dataset(meta)

                acao = upsert_dataset(cursor, dataset)

                if acao == "inserted":

                    inseridos += 1
                else:

                    atualizados += 1

            except Exception as e:

                falhas.append({"id": idr, "error": str(e)})

        conexao.commit()

    except Exception as e:

        if conexao is not None:

            conexao.rollback()

        if silent:

            return erro(str(e))

        console.print(f"[red]Erro ao exportar para dataset:[/red] {e}")

        return False

    finally:

        if cursor is not None:

            cursor.close()

        if conexao is not None:

            conexao.close()

    sucesso = (len(falhas) == 0)

    resultado = {
        "success": sucesso,
        "total_ids": len(ids),
        "inseridos": inseridos,
        "atualizados": atualizados,
        "falhas": falhas,
        "tabela": "brapci_elastic.dataset"
    }

    if silent:

        return resultado

    tabela = Table()
    tabela.add_column("IDs", style="cyan")
    tabela.add_column("Inseridos", style="green")
    tabela.add_column("Atualizados", style="yellow")
    tabela.add_column("Falhas", style="red")

    tabela.add_row(str(len(ids)), str(inseridos), str(atualizados),
                   str(len(falhas)))

    console.print(tabela)

    if len(falhas) > 0:

        for falha in falhas:

            console.print(f"[red]ID {falha['id']}:[/red] {falha['error']}")

    return resultado
