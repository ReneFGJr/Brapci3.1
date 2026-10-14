"""Preenche IDs ausentes no dataset usando RDFmetadata::metadata pela API v1."""

import json
import os
from pathlib import Path

import pymysql
import requests
from dotenv import load_dotenv

TASK = {
    "id": 2002,
    "name": "Atualizar dataset a partir do RDF",
    "description": "Insere trabalhos RDF ainda ausentes no dataset, usando metadata PHP.",
    "patterns": ["elastic update_dataset"],
    "parameters": [],
}
CLASSES = ("Article", "Proceedings", "Book", "ChapterBook", "Proceeding", "BookChapter")
BATCH_SIZE = 100


def _names(items):
    return "; ".join(str(item.get("name", "")) if isinstance(item, dict) else str(item)
                     for item in (items or []) if item)


def _integer(value):
    try:
        return int(value or 0)
    except (ValueError, TypeError):
        return 0


def dataset_values(metadata, work_id):
    if not isinstance(metadata, dict) or _integer(metadata.get("ID")) != work_id:
        raise ValueError("Metadata não corresponde ao ID solicitado (cancelado ou remissivo).")
    work_class = metadata.get("Class")
    if work_class not in CLASSES or not metadata.get("title"):
        raise ValueError("Metadata sem classe de trabalho ou título válido.")
    issue = metadata.get("Issue") or {}
    subject = metadata.get("subject") or {}
    return {
        "ID": work_id,
        "CLASS": work_class,
        "COLLECTION": {"Book": "BK", "ChapterBook": "BC", "BookChapter": "BC",
                       "Proceedings": "EV", "Proceeding": "EV"}.get(work_class, "JA"),
        "json": json.dumps(metadata, ensure_ascii=False, allow_nan=False),
        "DOI": metadata.get("DOI") or metadata.get("doi") or "",
        "JOURNAL": _integer(issue.get("jnl_rdf")),
        "ISSUE": _integer(issue.get("ID")),
        "PUBLICATION": metadata.get("publisher") or "",
        "TITLE": metadata["title"],
        "AUTHORS": _names(metadata.get("creator_author")),
        "KEYWORDS": _names(subject.get("pt")),
        "KEYWORD_EN": _names(subject.get("en")),
        "KEYWORD_ES": _names(subject.get("es")),
        "KEYWORD_FR": _names(subject.get("fr")),
        "ABSTRACTS": metadata.get("description") or "",
        "SESSION": _names(metadata.get("section")),
        "LEGEND": metadata.get("legend") or "",
        "YEAR": _integer(metadata.get("year")),
        "PDF": int(bool(metadata.get("resource_pdf"))),
        "URL": metadata.get("URL") or f"https://hdl.handle.net/20.500.11959/brapci/{work_id}",
        "COVER": metadata.get("cover") or "",
        "new": 1,
        "status": 1,
    }


def update_dataset(connection, session, endpoint, silent=False):
    result = {"success": True, "checked": 0, "inserted": 0, "existing": 0, "failed": 0, "errors": []}
    last_id = 0
    marks = ",".join(["%s"] * len(CLASSES))
    while True:
        with connection.cursor() as cursor:
            cursor.execute(
                "SELECT r.id_cc AS ID, EXISTS(SELECT 1 FROM brapci_elastic.dataset d "
                "WHERE d.ID = r.id_cc) AS present FROM brapci_rdf.rdf_concept r "
                "JOIN brapci_rdf.rdf_class c ON c.id_c = r.cc_class "
                f"WHERE c.c_class IN ({marks}) AND r.id_cc > %s ORDER BY r.id_cc LIMIT %s",
                (*CLASSES, last_id, BATCH_SIZE),
            )
            rows = cursor.fetchall()
        connection.commit()
        if not rows:
            break
        for row in rows:
            work_id = int(row["ID"])
            last_id = work_id
            result["checked"] += 1
            if row["present"]:
                result["existing"] += 1
                continue
            try:
                response = session.get(endpoint.format(ID=work_id), timeout=60)
                response.raise_for_status()
                values = dataset_values(response.json(), work_id)
                columns = ", ".join(f"`{column}`" for column in values)
                placeholders = ", ".join(["%s"] * len(values))
                # Revalida a existência na gravação; registros existentes não são atualizados.
                with connection.cursor() as cursor:
                    cursor.execute(
                        f"INSERT INTO brapci_elastic.dataset ({columns}, updated_at) "
                        f"SELECT {placeholders}, NOW() FROM DUAL WHERE NOT EXISTS "
                        "(SELECT 1 FROM brapci_elastic.dataset WHERE ID = %s)",
                        (*values.values(), work_id),
                    )
                    inserted = cursor.rowcount
                connection.commit()
                result["inserted" if inserted else "existing"] += 1
                if not silent:
                    print(f"ID {work_id}: {'inserido' if inserted else 'já existente'}", flush=True)
            except (requests.RequestException, pymysql.MySQLError, ValueError, TypeError) as error:
                connection.rollback()
                result["failed"] += 1
                result["success"] = False
                if len(result["errors"]) < 20:
                    result["errors"].append({"ID": work_id, "error": str(error)})
                if not silent:
                    print(f"ID {work_id}: erro: {error}", flush=True)
        if not silent:
            print(f"Verificados: {result['checked']} | Inseridos: {result['inserted']} | "
                  f"Existentes: {result['existing']} | Erros: {result['failed']}", flush=True)
    return result


def run(parametros=None, chat=None, silent=False):
    if parametros and parametros != ["update_dataset"]:
        return {"success": False, "error": "Uso: elastic update_dataset (task 2002)"}
    load_dotenv(Path(__file__).resolve().parent.parent / ".env")
    endpoint = os.getenv("BRAPCI_METADATA_URL", "https://cip.brapci.inf.br/api/brapci/get/v1/{ID}")
    if "{ID}" not in endpoint:
        return {"success": False, "error": "BRAPCI_METADATA_URL deve conter {ID}."}
    connection = None
    try:
        connection = pymysql.connect(
            host=os.getenv("DB_HOST", "localhost"), port=int(os.getenv("DB_PORT", 3306)),
            user=os.getenv("DB_USERNAME"), password=os.getenv("DB_PASSWORD"),
            database="brapci_elastic", charset="utf8mb4",
            cursorclass=pymysql.cursors.DictCursor, autocommit=False,
        )
        if not silent:
            print("TASK 2002: verificando RDF e preenchendo IDs ausentes no dataset...", flush=True)
        with requests.Session() as session:
            session.headers.update({"User-Agent": "CURADOR/2002"})
            result = update_dataset(connection, session, endpoint, silent=silent)
        if not silent:
            print(f"Concluído: {result['inserted']} inseridos; {result['existing']} existentes; {result['failed']} erros.", flush=True)
        return result
    except (pymysql.MySQLError, ValueError) as error:
        if not silent:
            print(f"Erro na atualização do dataset: {error}", flush=True)
        return {"success": False, "error": str(error)}
    finally:
        if connection is not None:
            connection.close()
