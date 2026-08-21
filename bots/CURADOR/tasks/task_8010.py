#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import os
import sys
import csv
import re
from pathlib import Path

import pymysql
from dotenv import load_dotenv

TASK = {
    "id": 8010,
    "name": "Journals",
    "description": "Journals cadastrados na BRAPCI.",
    "patterns": [
        "ISSN",
        "journals",
    ],
    "parameters": [{
        "name": "fonte",
        "type": "string",
        "required": False
    }]
}

# ============================================================
# CONFIGURAÇÃO
# ============================================================

# Localiza o .env na raiz do CURADOR
BASE_DIR = Path(__file__).resolve().parent.parent
load_dotenv(BASE_DIR / ".env")

ARQUIVO_PATH = "../../../_Documments/Qualis"

HEADERS = {"User-Agent": "Journals-Checker/1.0"}

SILENT = False


def log(*args, **kwargs):
    if not SILENT:
        print(*args, **kwargs)


# ============================================================
# FUNÇÕES BÁSICAS
# ============================================================


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
        database=database or os.getenv("DB_DATABASE", "brapci_journals"),
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=False,
    )


# ============================================================
# NORMALIZAÇÃO
# ============================================================


def normalize_issn(issn):
    """
    Normaliza ISSN para o formato 1234-567X.
    """

    if not issn:
        return None

    issn = str(issn).strip().upper()

    # Remove tudo que não seja número ou X
    clean = re.sub(r"[^0-9X]", "", issn)

    if len(clean) != 8:
        return None

    return clean[:4] + "-" + clean[4:]


def parse_period(period):
    """
    Converte:
        2010-2012 -> (2010, 2012)
        2024      -> (2024, 2024)
    """

    if not period:
        return None, None

    period = str(period).strip()

    years = re.findall(r"\d{4}", period)

    if len(years) == 1:
        year = int(years[0])
        return year, year

    if len(years) >= 2:
        return int(years[0]), int(years[1])

    return None, None


# ============================================================
# RANKING SOURCE
# ============================================================


def get_or_create_qualis(cursor):
    """
    Recupera ou cria a base estratificadora Qualis CAPES.
    """

    sql = """
        SELECT id_ranking_source
        FROM ranking_sources
        WHERE name = %s
        LIMIT 1
    """

    cursor.execute(sql, ("Qualis CAPES", ))
    row = cursor.fetchone()

    if row:
        return row["id_ranking_source"]

    sql = """
        INSERT INTO ranking_sources
        (
            name,
            acronym,
            institution,
            metric_type,
            description
        )
        VALUES (%s, %s, %s, %s, %s)
    """

    cursor.execute(sql, (
        "Qualis CAPES", "Qualis",
        "Coordenação de Aperfeiçoamento de Pessoal de Nível Superior - CAPES",
        "stratum",
        "Sistema brasileiro de classificação de periódicos utilizado pela CAPES."
    ))

    return cursor.lastrowid


# ============================================================
# PUBLICATION
# ============================================================


def find_publication_by_issn(cursor, issn):
    """
    Procura primeiro o ISSN em publication_issns.
    """

    sql = """
        SELECT
            p.id_publication,
            p.issn_l,
            p.title
        FROM publication_issns pi
        INNER JOIN publications p
            ON p.id_publication = pi.id_publication
        WHERE pi.issn = %s
        LIMIT 1
    """

    cursor.execute(sql, (issn, ))
    return cursor.fetchone()


def create_publication(cursor, issn, title):
    """
    Cria uma publicação ainda sem ISSN-L confirmado.

    O ISSN existente no arquivo Qualis NÃO é automaticamente
    considerado ISSN-L.
    """

    sql = """
        INSERT INTO publications
        (
            title
        )
        VALUES (%s)
    """

    cursor.execute(sql, (title, ))
    id_publication = cursor.lastrowid

    sql = """
        INSERT INTO publication_issns
        (
            id_publication,
            issn,
            medium,
            issn_status
        )
        VALUES (%s, %s, %s, %s)
    """

    cursor.execute(sql, (id_publication, issn, "other", "confirmed"))

    return id_publication


def get_or_create_publication(cursor, issn, title):
    """
    Recupera publicação existente pelo ISSN.
    Caso não exista, cria publications + publication_issns.
    """

    publication = find_publication_by_issn(cursor, issn)

    if publication:
        return publication["id_publication"], False

    id_publication = create_publication(cursor, issn, title)

    return id_publication, True


# ============================================================
# QUALIS
# ============================================================


def save_qualis(cursor, id_publication, id_ranking_source, stratum, area,
                period_start, period_end):
    """
    Insere ou atualiza o estrato Qualis.
    """

    sql = """
        INSERT INTO publication_rankings
        (
            id_publication,
            id_ranking_source,
            period_start,
            period_end,
            stratum,
            evaluation_area
        )
        VALUES (%s, %s, %s, %s, %s, %s)

        ON DUPLICATE KEY UPDATE
            stratum = VALUES(stratum),
            updated_at = CURRENT_TIMESTAMP
    """

    cursor.execute(sql, (id_publication, id_ranking_source, period_start,
                         period_end, stratum, area))


# ============================================================
# IMPORTAÇÃO DE ARQUIVO
# ============================================================


def import_qualis_file(cursor, filename, id_ranking_source):
    """
    Importa um arquivo Qualis.
    """

    total = 0
    created = 0
    rankings = 0
    errors = 0

    log()
    log("=" * 70)
    log(f"Arquivo: {filename.name}")
    log("=" * 70)

    # Tenta primeiro UTF-8 com BOM.
    # Caso os arquivos antigos estejam em latin-1,
    # faz nova tentativa.
    encodings = ["utf-8-sig", "latin-1"]

    file_handle = None

    for encoding in encodings:
        try:
            file_handle = open(filename, "r", encoding=encoding, newline="")

            # força leitura inicial para validar encoding
            file_handle.read(1024)
            file_handle.seek(0)
            break

        except UnicodeDecodeError:
            if file_handle:
                file_handle.close()

            file_handle = None

    if file_handle is None:
        log(f"ERRO: não foi possível ler {filename}")
        return 0, 0, 0, 1

    with file_handle as f:

        reader = csv.DictReader(f, delimiter=";")

        for row in reader:

            total += 1

            try:

                issn = normalize_issn(row.get("ISSN"))

                title = (row.get("Título") or row.get("Titulo") or "").strip()

                stratum = (row.get("Estrato") or "").strip()

                area = (row.get("Area") or row.get("Área") or "").strip()

                period = (row.get("PERIODO") or row.get("PERÍODO")
                          or "").strip()

                # --------------------------------------------
                # Validações
                # --------------------------------------------

                if not issn:
                    log(f"[IGNORADO] Linha {total + 1}: "
                        f"ISSN inválido")
                    errors += 1
                    continue

                if not title:
                    log(f"[IGNORADO] {issn}: "
                        f"título vazio")
                    errors += 1
                    continue

                period_start, period_end = parse_period(period)

                if not period_start:
                    log(f"[IGNORADO] {issn}: "
                        f"período inválido ({period})")
                    errors += 1
                    continue

                # --------------------------------------------
                # PUBLICATION
                # --------------------------------------------

                id_publication, was_created = \
                    get_or_create_publication(
                        cursor,
                        issn,
                        title
                    )

                if was_created:
                    created += 1

                # --------------------------------------------
                # QUALIS
                # --------------------------------------------

                save_qualis(cursor, id_publication, id_ranking_source, stratum,
                            area, period_start, period_end)

                rankings += 1

                log(f"[OK] {issn} | "
                    f"{stratum} | "
                    f"{period_start}-{period_end} | "
                    f"{title}")

            except Exception as e:

                errors += 1

                log(f"[ERRO] Linha {total + 1}: {e}")

    return total, created, rankings, errors


# ============================================================
# IMPORTAÇÃO QUALIS
# ============================================================


def qualisImport():

    path = (Path(__file__).resolve().parent / ARQUIVO_PATH).resolve()

    log()
    log("Importação Qualis CAPES")
    log(f"Diretório: {path}")
    log()

    if not path.exists():
        return erro(f"Diretório não encontrado: {path}")

    # Aceita TXT e CSV
    files = sorted(list(path.glob("*.txt")) + list(path.glob("*.csv")))

    if not files:
        return erro(f"Nenhum arquivo encontrado em {path}")

    connection = None

    try:

        connection = get_connection("brapci_journals")

        with connection.cursor() as cursor:

            # --------------------------------------------
            # Base estratificadora
            # --------------------------------------------

            id_ranking_source = \
                get_or_create_qualis(cursor)

            connection.commit()

            log("Qualis CAPES: "
                f"id={id_ranking_source}")

            log(f"Arquivos encontrados: {len(files)}")

            total_all = 0
            created_all = 0
            rankings_all = 0
            errors_all = 0

            # --------------------------------------------
            # Processa todos os arquivos
            # --------------------------------------------

            for filename in files:

                try:

                    total, created, rankings, errors = \
                        import_qualis_file(
                            cursor,
                            filename,
                            id_ranking_source
                        )

                    connection.commit()

                    total_all += total
                    created_all += created
                    rankings_all += rankings
                    errors_all += errors

                except Exception as e:

                    connection.rollback()

                    log(f"[ERRO ARQUIVO] "
                        f"{filename.name}: {e}")

                    errors_all += 1

            # --------------------------------------------
            # RESULTADO
            # --------------------------------------------

            log()
            log("=" * 70)
            log("RESUMO")
            log("=" * 70)

            log(f"Registros lidos:       {total_all}")

            log(f"Publicações criadas:   {created_all}")

            log(f"Avaliações importadas: {rankings_all}")

            log(f"Erros/ignorados:       {errors_all}")

            return {
                "success": True,
                "files": len(files),
                "records": total_all,
                "publications_created": created_all,
                "rankings": rankings_all,
                "errors": errors_all,
            }

    except Error as e:

        if connection:
            connection.rollback()

        return erro(str(e))

    except Exception as e:

        if connection:
            connection.rollback()

        return erro(str(e))

    finally:

        if connection:
            connection.close()


# ============================================================
# RUN
# ============================================================


def run(parametros=None, chat=None, silent=False):

    global SILENT
    SILENT = silent

    if parametros is None:
        parametros = []

    action = (parametros[0].lower() if len(parametros) > 0 else "status")

    log(f"Acao: {action}")

    if action == "qualis":
        return qualisImport()

    if action == "status":
        return {"success": True, "task": TASK["name"], "status": "ready"}

    return erro(f"Ação desconhecida: {action}")


if __name__ == "__main__":

    resultado = run(parametros=sys.argv[2:], silent=False)

    print()
    print(resultado)
