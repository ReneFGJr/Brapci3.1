import json
import os
from pathlib import Path
import unicodedata

import pymysql
import requests
from dotenv import load_dotenv

TASK = {
    "id": 2000,
    "name": "Exportar para o Elastic Search",
    "description": "Exporta o Dataset para o Elasticsearch, com filtro opcional por JOURNAL.",
    "patterns": [
        "elastic export",
    ],
    "parameters": [
        {
            "name": "journal",
            "type": "integer",
            "required": False
        }
    ]
}

def excludeElastic():
    """Reservado para implementação da exclusão no Elasticsearch."""


def _elastic_document(row):
    """Converte um registro do Dataset para o formato de busca da BRAPCI."""
    metadata = row.get("json") or {}
    if isinstance(metadata, (str, bytes)):
        try:
            metadata = json.loads(metadata)
        except (ValueError, UnicodeDecodeError):
            metadata = {}
    if not isinstance(metadata, dict):
        metadata = {}

    def values(value):
        if isinstance(value, dict):
            if "name" in value:
                return values(value["name"])
            return [text for item in value.values() for text in values(item)]
        if isinstance(value, (list, tuple)):
            return [text for item in value for text in values(item)]
        if value is None:
            return []
        text = str(value).strip()
        return [text] if text else []

    def ascii_text(text):
        return "".join(char for char in unicodedata.normalize("NFKD", text)
                       if not unicodedata.combining(char))

    def collect(field, columns, split=False, normalize=True):
        texts = values(metadata.get(field))
        if not texts:
            for column in columns:
                raw = values(row.get(column))
                texts.extend(part.strip() for text in raw for part in
                             (text.split(";") if split else [text]) if part.strip())
        if normalize:
            texts = [ascii_text(text).lower() for text in texts]
        return list(dict.fromkeys(texts))

    titles = collect("Title", ["TITLE"])
    keywords = collect("Subject", ["KEYWORDS", "KEYWORDS_EN", "KEYWORDS_ES", "KEYWORDS_FR"], split=True)
    abstracts = collect("Abstract", ["ABSTRACTS"])
    authors = collect("Authors", ["AUTHORS"], split=True, normalize=False)
    sections = collect("Sections", ["SESSION", "SESSION_SUB"], split=True)
    languages = values(metadata.get("Idioma")) or values(row.get("LANGUAGE"))
    full = titles + keywords + abstracts
    for author in authors:
        full.extend([ascii_text(author), ascii_text(author).lower()])

    identifier = int(row["ID"])
    return {
        "id": identifier,
        "keyword": keywords,
        "abstract": abstracts,
        "authors": authors,
        "title": titles,
        "journal": str(row.get("JOURNAL") or ""),
        "year": str(row.get("YEAR") or ""),
        "type": row.get("CLASS") or "",
        "section": sections,
        "language": languages,
        "full": " ".join(full).strip(),
        "collection": row.get("COLLECTION") or "",
        "DOI": row.get("DOI") or "",
        "URL": row.get("URL") or f"https://hdl.handle.net/20.500.11959/brapci/{identifier}",
    }

def exportToElastic(journal=0):
    """Exporta todos os registros ou apenas os registros do JOURNAL informado."""
    journal = int(journal)
    if journal < 0:
        raise ValueError("O ID do JOURNAL deve ser maior ou igual a zero.")

    load_dotenv(Path(__file__).resolve().parent.parent / ".env")
    server = (os.getenv("ElasticServer") or "").strip().rstrip("/")
    index = (os.getenv("ElaseicSearchIndex") or "").strip().lower()
    if not server or not index:
        raise ValueError("Configure ElasticServer e ElaseicSearchIndex no .env do CURADOR.")

    result = {
        "success": True,
        "journal": journal,
        "index": index,
        "total": 0,
        "exported": 0,
        "failed": 0,
        "errors": [],
    }

    conn = pymysql.connect(
        host=os.getenv("DB_HOST", "localhost"),
        port=int(os.getenv("DB_PORT", 3306)),
        user=os.getenv("DB_USERNAME"),
        password=os.getenv("DB_PASSWORD"),
        database="brapci_elastic",
        charset="utf8mb4",
        cursorclass=pymysql.cursors.SSDictCursor,
    )
    try:
        with conn.cursor() as cursor, requests.Session() as session:
            sql = "SELECT * FROM dataset"
            if journal > 0:
                cursor.execute(sql + " WHERE JOURNAL = %s ORDER BY ID", (journal,))
            else:
                cursor.execute(sql + " ORDER BY ID")

            while True:
                rows = cursor.fetchmany(500)
                if not rows:
                    break

                lines = []
                for row in rows:
                    lines.append(json.dumps({"index": {"_index": index, "_id": str(row["ID"])}}))
                    lines.append(json.dumps(_elastic_document(row), ensure_ascii=False, allow_nan=False))

                response = session.post(
                    f"{server}/_bulk",
                    data=("\n".join(lines) + "\n").encode("utf-8"),
                    headers={"Content-Type": "application/x-ndjson"},
                    timeout=int(os.getenv("TIMEOUT", 300)),
                )
                response.raise_for_status()
                payload = response.json()
                items = payload.get("items", [])
                if len(items) != len(rows):
                    raise RuntimeError("Resposta incompleta do Elasticsearch para o lote enviado.")

                result["total"] += len(rows)
                for item in items:
                    operation = item["index"]
                    if 200 <= operation.get("status", 0) < 300 and "error" not in operation:
                        result["exported"] += 1
                    else:
                        result["failed"] += 1
                        if len(result["errors"]) < 20:
                            result["errors"].append({
                                "ID": operation.get("_id"),
                                "error": operation.get("error", "Falha ao indexar documento."),
                            })
        result["success"] = result["failed"] == 0
        return result
    finally:
        conn.close()



def run(parametros=None,chat=None,silent=False):

    parametros = parametros or []
    if len(parametros) > 1:
        return {"success": False, "error": "Uso: 2000 [ID do JOURNAL]"}
    try:
        journal = int(parametros[0]) if parametros else 0
        if not silent:
            print("Iniciando exportação para o Elasticsearch...")
        return exportToElastic(journal)
    except (TypeError, ValueError) as error:
        return {"success": False, "error": str(error)}
