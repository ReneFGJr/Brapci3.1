import json
import os
from pathlib import Path
import unicodedata

import pymysql
import requests
from dotenv import load_dotenv
from rich import box
from rich.console import Group
from rich.panel import Panel
from rich.text import Text
from rich.progress import Progress, BarColumn, TextColumn, TimeRemainingColumn

class _ExportProgress(Progress):
    def get_renderable(self):
        task = self.tasks[0] if self.tasks else None
        details = Text()
        if task is not None:
            details.append(task.description + "\n", style="bold cyan")
            details.append(f"Pendentes no início: {task.total}\n", style="cyan")
            details.append(f"Exportados: {task.fields['exported']}", style="bold green")
            details.append(f"   Erros: {task.fields['failed']}", style="bold red")
        return Panel(
            Group(details, super().get_renderable()),
            title="CURADOR • TASK 2000 • Exportar dataset",
            box=box.DOUBLE, border_style="cyan", width=70,
        )


TASK = {
    "id": 2000,
    "name": "Exportar para o Elastic Search",
    "description": "Exporta o Dataset para o Elasticsearch, com filtro opcional por JOURNAL.",
    "patterns": [
        "elastic export",
        "elastic delete",
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
    """Exclui o índice configurado, incluindo documentos e mapeamentos."""
    load_dotenv(Path(__file__).resolve().parent.parent / ".env")
    server = (os.getenv("ElasticServer") or "").strip().rstrip("/")
    index = (os.getenv("ElaseicSearchIndex") or "").strip().lower()
    if not server or not index:
        return {"success": False, "error": "Configure ElasticServer e ElaseicSearchIndex no .env do CURADOR."}
    if index == "_all" or any(char in index for char in '*?,/\\#%'):
        return {"success": False, "error": "Informe um único nome de índice em ElaseicSearchIndex."}

    try:
        response = requests.delete(
            f"{server}/{index}",
            timeout=int(os.getenv("TIMEOUT", 300)),
        )
        response.raise_for_status()
        payload = response.json()
        acknowledged = payload.get("acknowledged") is True
        result = {"success": acknowledged, "index": index, "acknowledged": acknowledged}
        if not acknowledged:
            result["error"] = "O Elasticsearch não confirmou a exclusão do índice."
            return result

        conn = pymysql.connect(
            host=os.getenv("DB_HOST", "localhost"),
            port=int(os.getenv("DB_PORT", 3306)),
            user=os.getenv("DB_USERNAME"),
            password=os.getenv("DB_PASSWORD"),
            database="brapci_elastic",
            charset="utf8mb4",
        )
        try:
            with conn.cursor() as cursor:
                cursor.execute("UPDATE dataset SET `new` = 0")
                result["updated"] = cursor.rowcount
            conn.commit()
        finally:
            conn.close()
        return result
    except (requests.RequestException, pymysql.MySQLError, ValueError) as error:
        return {"success": False, "index": index, "error": str(error)}


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
    section_types = collect("SECTIONSUB", ["SECTIONSUB", "SESSION_SUB"], split=True)
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
        "section_type": section_types,
        "language": languages,
        "full": " ".join(full).strip(),
        "collection": row.get("COLLECTION") or "",
        "DOI": row.get("DOI") or "",
        "URL": row.get("URL") or f"https://hdl.handle.net/20.500.11959/brapci/{identifier}",
    }

def exportToElastic(journal=0, silent=False):
    """Exporta registros com new = 1, com filtro opcional por JOURNAL."""
    journal = int(journal or 0)
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

    journal_filter = " AND JOURNAL = %s" if journal else ""
    journal_parameters = (journal,) if journal else ()

    conn = pymysql.connect(
        host=os.getenv("DB_HOST", "localhost"),
        port=int(os.getenv("DB_PORT", 3306)),
        user=os.getenv("DB_USERNAME"),
        password=os.getenv("DB_PASSWORD"),
        database="brapci_elastic",
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
    )
    try:
        with conn.cursor() as cursor:
            cursor.execute(
                "SELECT COUNT(*) AS total FROM dataset WHERE `new` = 1" + journal_filter,
                journal_parameters,
            )
            pending = int(cursor.fetchone()['total'])
        conn.commit()
        with requests.Session() as session, _ExportProgress(
            BarColumn(bar_width=30, style='blue', complete_style='green', finished_style='bold green'),
            TextColumn('[bold yellow]{task.percentage:>6.1f}%'),
            TimeRemainingColumn(),
            disable=silent,
        ) as progress:
            progress_task = progress.add_task(
                f'JOURNAL {journal}' if journal else 'Todos os registros pendentes', total=pending, exported=0, failed=0,
            )
            last_id = None
            while True:
                conn.ping(reconnect=True)
                with conn.cursor() as cursor:
                    boundary = " AND ID > %s" if last_id is not None else ""
                    parameters = journal_parameters + ((last_id,) if last_id is not None else ())
                    cursor.execute(
                        "SELECT * FROM dataset WHERE `new` = 1" + journal_filter + boundary
                        + " ORDER BY ID LIMIT 1000",
                        parameters,
                    )
                    rows = cursor.fetchall()
                if not rows:
                    break

                last_id = int(rows[-1]["ID"])
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
                exported_ids = []
                for row, item in zip(rows, items):
                    operation = item["index"]
                    if 200 <= operation.get("status", 0) < 300 and "error" not in operation:
                        result["exported"] += 1
                        exported_ids.append(row["ID"])
                    else:
                        result["failed"] += 1
                        if len(result["errors"]) < 20:
                            result["errors"].append({
                                "ID": operation.get("_id"),
                                "error": operation.get("error", "Falha ao indexar documento."),
                            })

                if exported_ids:
                    conn.ping(reconnect=True)
                    with conn.cursor() as update_cursor:
                        update_cursor.executemany(
                            "UPDATE dataset SET `new` = 0 WHERE ID = %s",
                            [(identifier,) for identifier in exported_ids],
                        )
                    conn.commit()
                progress.update(
                    progress_task, completed=result['exported'],
                    exported=result['exported'], failed=result['failed'],
                    refresh=True,
                )
        result["success"] = result["failed"] == 0
        return result
    finally:
        conn.close()



def run(parametros=None,chat=None,silent=False):

    parametros = parametros or []
    if not parametros:
        if not silent:
            print("sem parametros")
        return {"success": False, "error": "sem parametros"}
    usage = "Uso: 2000 delete | 2000 export [ID do JOURNAL]"
    try:
        action = str(parametros[0]).strip().lower()
        if action not in ("delete", "export"):
            return {"success": False, "error": usage}
        arguments = parametros[1:]
        if action == "delete":
            if arguments:
                return {"success": False, "error": usage}
            return excludeElastic()
        if len(arguments) > 1:
            return {"success": False, "error": usage}
        journal = int(arguments[0] or 0) if arguments else 0
        if not silent:
            print("Iniciando exportação para o Elasticsearch...")
        return exportToElastic(journal, silent=silent)
    except (TypeError, ValueError) as error:
        return {"success": False, "error": str(error)}
