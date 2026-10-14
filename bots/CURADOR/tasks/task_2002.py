"""Preenche IDs ausentes no dataset usando RDFmetadata::metadata pela API v3."""

import json
import os
import time
from pathlib import Path

import pymysql
import requests
from dotenv import load_dotenv
from rich.live import Live
from rich.panel import Panel
from rich.table import Table
from rich.text import Text
from rich.console import Group

TASK = {
    "id": 2002,
    "name": "Atualizar dataset a partir do RDF",
    "description": "Insere trabalhos RDF ainda ausentes no dataset, usando metadata PHP.",
    "patterns": ["elastic update_dataset"],
    "parameters": [],
}
CLASSES = ("Article", "Proceedings", "Book", "ChapterBook", "Proceeding", "BookChapter")
BATCH_SIZE = 100
RECORD_PAUSE_SECONDS = 20


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
        returned_id = metadata.get('ID', 'ausente') if isinstance(metadata, dict) else 'ausente'
        message = metadata.get('message', '') if isinstance(metadata, dict) else ''
        raise ValueError(f"Metadata não corresponde ao ID solicitado: solicitado {work_id}, "
                         f"retornado {returned_id}. {message}".strip())
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
        "KEYWORDS_EN": _names(subject.get("en")),
        "KEYWORDS_ES": _names(subject.get("es")),
        "KEYWORDS_FR": _names(subject.get("fr")),
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


def _metadata_panel(metadata):
    metadata = metadata if isinstance(metadata, dict) else {}
    subject = metadata.get('subject') or {}
    keywords = '; '.join(_names(items) for items in subject.values()) if isinstance(subject, dict) else _names(subject)
    table = Table.grid(padding=(0, 2))
    table.add_column(style='bold cyan', width=16)
    table.add_column(overflow='fold')
    for label, value, color in [
        ('ID retornado', metadata.get('ID', 'Não informado'), 'yellow'),
        ('Classe', metadata.get('Class', 'Não informada'), 'magenta'),
        ('Título', metadata.get('title') or 'Não informado', 'green'),
        ('Autores', _names(metadata.get('creator_author')) or 'Não informados', 'cyan'),
        ('Palavras-chave', keywords or 'Não informadas', 'blue'),
    ]:
        table.add_row(label, Text(str(value), style=color))
    return Panel(table, title='Metadados retornados pela API', border_style='cyan')


def _status_panel(result, row=None, title='', status='Buscando conceitos ausentes', color='cyan'):
    row = row or {}
    table = Table.grid(padding=(0, 2))
    table.add_column(style='bold cyan', width=14)
    table.add_column(overflow='ellipsis', no_wrap=True)
    table.add_row('ID', Text(str(row.get('ID', '—')), style='bold yellow'))
    table.add_row('Classe', Text(str(row.get('Class', '—')), style='bold magenta'))
    table.add_row('Título', Text(' '.join(str(title).split()) or '—', style='bold green'))
    table.add_row('Status', Text(' '.join(str(status).split()), style=color))
    table.add_row('Processados', str(result['checked']))
    table.add_row('Inseridos', Text(str(result['inserted']), style='green'))
    table.add_row('Já existentes', str(result['existing']))
    table.add_row('Erros', Text(str(result['failed']), style='red'))
    panel = Panel(table, title='CURADOR • TASK 2002 • Atualizar dataset', border_style='cyan')
    problem = result.get('last_error')
    if problem:
        return Group(panel, _metadata_panel(problem.get('metadata')), Panel(
            Text(f"ID {problem['ID']}: {problem['error']}", style='bold red', overflow='fold'),
            title='Último problema', border_style='red',
        ))
    return panel


def update_dataset(connection, session, endpoint, silent=False):
    if silent:
        return _update_dataset(connection, session, endpoint)
    initial = dict(checked=0, inserted=0, existing=0, failed=0)
    with Live(_status_panel(initial), refresh_per_second=4) as live:
        def show(result, row=None, title='', status='Buscando conceitos ausentes', color='cyan'):
            live.update(_status_panel(result, row, title, status, color), refresh=True)
        result = _update_dataset(connection, session, endpoint, show)
        show(result, status='Concluído', color='green' if result['success'] else 'yellow')
        return result


def _update_dataset(connection, session, endpoint, show=None):
    result = {"success": True, "checked": 0, "inserted": 0, "existing": 0, "failed": 0, "errors": []}
    last_id = None
    processed = False
    while True:
        boundary = '' if last_id is None else 'AND r.id_cc < %s '
        parameters = (BATCH_SIZE,) if last_id is None else (last_id, BATCH_SIZE)
        with connection.cursor() as cursor:
            cursor.execute(
                "SELECT r.id_cc AS ID, r.cc_class AS Class FROM brapci_rdf.rdf_concept r "
                "LEFT JOIN brapci_elastic.dataset d ON d.ID = r.id_cc "
                "WHERE r.cc_class = 10 AND r.cc_status < 9 AND d.ID IS NULL "
                + boundary + "ORDER BY r.id_cc DESC LIMIT %s",
                parameters,
            )
            rows = cursor.fetchall()
        connection.commit()
        if not rows:
            break
        for row in rows:
            if processed:
                if show:
                    show(result, status=f'Aguardando {RECORD_PAUSE_SECONDS}s antes do próximo registro', color='yellow')
                time.sleep(RECORD_PAUSE_SECONDS)
            processed = True
            work_id = int(row["ID"])
            last_id = work_id
            result["checked"] += 1
            title = ''
            metadata = None
            if show:
                show(result, row, status='Recuperando metadata')
            try:
                response = session.get(endpoint.format(ID=work_id), timeout=60)
                response.raise_for_status()
                metadata = response.json()
                if isinstance(metadata, dict):
                    title = metadata.get('title') or ''
                    row['Class'] = metadata.get('Class') or row['Class']
                values = dataset_values(metadata, work_id)
                title = values['TITLE']
                if show:
                    show(result, row, title, 'Gravando dataset', 'yellow')
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
                if show:
                    show(result, row, title, 'Inserido' if inserted else 'Já existente', 'green')
            except (requests.RequestException, pymysql.MySQLError, ValueError, TypeError) as error:
                connection.rollback()
                result["failed"] += 1
                result["success"] = False
                result['last_error'] = {"ID": work_id, "error": str(error), "metadata": metadata}
                if len(result["errors"]) < 20:
                    result["errors"].append({"ID": work_id, "error": str(error)})
                if show:
                    show(result, row, title, 'Erro ao atualizar', 'red')
    return result


def run(parametros=None, chat=None, silent=False):
    if parametros and parametros != ["update_dataset"]:
        return {"success": False, "error": "Uso: elastic update_dataset (task 2002)"}
    load_dotenv(Path(__file__).resolve().parent.parent / ".env")
    endpoint = os.getenv("BRAPCI_METADATA_URL", "https://cip.brapci.inf.br/api/brapci/get/v3/{ID}")
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
        with requests.Session() as session:
            session.headers.update({"User-Agent": "CURADOR/2002"})
            result = update_dataset(connection, session, endpoint, silent=silent)
        return result
    except (pymysql.MySQLError, ValueError) as error:
        if not silent:
            print(f"Erro na atualização do dataset: {error}", flush=True)
        return {"success": False, "error": str(error)}
    finally:
        if connection is not None:
            connection.close()
