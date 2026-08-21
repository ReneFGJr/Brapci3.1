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
ARQUIVO_PATH_SJR = "../../../_Documments/sjr"
ARQUIVO_PATH_ISSNL = \
    "../../../_Documments/ISSN/issnltables/20260820.ISSN-to-ISSN-L.txt"

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


def parse_sjr_value(value):
    """
    Converte SJR textual (ex.: 1,245) para float.
    """

    if value is None:
        return None

    text = str(value).strip().replace(" ", "")

    if not text:
        return None

    text = text.replace(".", "").replace(",", ".")

    try:
        return float(text)
    except ValueError:
        return None


def extract_issns(issn_raw):
    """
    Extrai todos os ISSNs válidos da célula SJR.
    """

    if not issn_raw:
        return []

    issns = []

    for chunk in str(issn_raw).split(","):
        normalized = normalize_issn(chunk)
        if normalized and normalized not in issns:
            issns.append(normalized)

    return issns


def extract_year_from_filename(filename):
    """
    Recupera o ano no nome do arquivo scimagojr.
    """

    years = re.findall(r"\b(19\d{2}|20\d{2})\b", filename)
    if not years:
        return None

    return int(years[-1])


def parse_issnl_type(value):
    """
    Normaliza o campo type (char(2)) da tabela issn_l.
    """

    text = (value or "").strip().lower()
    if not text:
        return "ta"

    return text[:2]


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


def get_or_create_sjr(cursor):
    """
    Recupera ou cria a base de ranking SJR (Scopus).
    """

    sql = """
        SELECT id_ranking_source
        FROM ranking_sources
        WHERE name = %s
        LIMIT 1
    """

    cursor.execute(sql, ("SJR Scopus", ))
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
        "SJR Scopus", "SJR", "Scopus / SCImago",
        "score",
        "SCImago Journal Rank (SJR) da base Scopus."
    ))

    return cursor.lastrowid


# ============================================================
# PUBLICATION
# ============================================================


def resolve_issn_l(cursor, issn):
    """
    Retorna o ISSN-L associado ao ISSN ou o próprio ISSN quando não
    houver correspondência na tabela de equivalência.
    """

    sql = """
        SELECT issn_l
        FROM issn_l
        WHERE issn = %s
        LIMIT 1
    """

    cursor.execute(sql, (issn, ))
    row = cursor.fetchone()

    if not row:
        return issn

    return normalize_issn(row.get("issn_l")) or issn


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
    Cria uma publicação com ISSN-L igual ao ISSN informado.
    """

    sql = """
        INSERT INTO publications
        (
            issn,
            issn_l,
            title
        )
        VALUES (%s, %s, %s)
    """

    cursor.execute(sql, (issn, issn, title))
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
        if not publication.get("issn_l"):
            sql = """
                UPDATE publications
                SET issn_l = %s, issn = %s
                WHERE id_publication = %s
                  AND (issn_l IS NULL OR issn_l = '')
            """
            cursor.execute(sql, (issn, issn, publication["id_publication"]))
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


def save_sjr(cursor, id_publication, id_ranking_source, year, quartile,
             sjr_value, area, notes):
    """
    Insere ou atualiza SJR na publication_rankings.
    """

    sql = """
        INSERT INTO publication_rankings
        (
            id_publication,
            id_ranking_source,
            period_start,
            period_end,
            stratum,
            numeric_value,
            evaluation_area,
            notes
        )
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s)

        ON DUPLICATE KEY UPDATE
            stratum = VALUES(stratum),
            numeric_value = VALUES(numeric_value),
            notes = VALUES(notes),
            updated_at = CURRENT_TIMESTAMP
    """

    cursor.execute(sql, (id_publication, id_ranking_source, year, year,
                         quartile, sjr_value, area, notes))


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

                issn = resolve_issn_l(cursor, issn)

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


def import_sjr_file(cursor, filename, id_ranking_source):
    """
    Importa um arquivo SJR (Scopus).
    """

    total = 0
    created = 0
    rankings = 0
    errors = 0

    year = extract_year_from_filename(filename.name)
    if not year:
        log(f"[IGNORADO] arquivo sem ano no nome: {filename.name}")
        return 0, 0, 0, 1

    log()
    log("=" * 70)
    log(f"Arquivo: {filename.name}")
    log("=" * 70)

    with open(filename, "r", encoding="utf-8-sig", newline="") as f:
        reader = csv.DictReader(f, delimiter=";")

        for row in reader:
            total += 1

            try:
                title = (row.get("Title") or "").strip().strip('"')
                quartile = (row.get("SJR Quartile") or "").strip()
                sjr_value = parse_sjr_value(row.get("SJR"))
                sourceid = (row.get("Sourceid") or "").strip()
                coverage = (row.get("Coverage") or "").strip()
                area = "Library and Information Sciences"

                issns = extract_issns(row.get("Issn"))

                if not issns:
                    log(f"[IGNORADO] Linha {total + 1}: ISSN inválido")
                    errors += 1
                    continue

                # A tabela de rankings e o cadastro da publicação devem
                # usar o identificador canônico. Mais de um ISSN da mesma
                # linha pode apontar para o mesmo ISSN-L.
                issns_l = []
                for issn in issns:
                    issn_l = resolve_issn_l(cursor, issn)
                    if issn_l not in issns_l:
                        issns_l.append(issn_l)
                issns = issns_l

                if not title:
                    log(f"[IGNORADO] {issns[0]}: título vazio")
                    errors += 1
                    continue

                if sjr_value is None:
                    log(f"[IGNORADO] {issns[0]}: SJR inválido")
                    errors += 1
                    continue

                publication = None
                matched_issn = None

                for issn in issns:
                    publication = find_publication_by_issn(cursor, issn)
                    if publication:
                        matched_issn = issn
                        break

                if not publication:
                    matched_issn = issns[0]
                    id_publication = create_publication(cursor, matched_issn,
                                                        title)
                    created += 1
                else:
                    id_publication = publication["id_publication"]

                notes = f"sourceid={sourceid}; coverage={coverage}"

                save_sjr(cursor, id_publication, id_ranking_source, year,
                         quartile, sjr_value, area, notes)

                rankings += 1

                log(f"[OK] {matched_issn} | {year} | {quartile} | "
                    f"SJR={sjr_value:.4f} | {title}")

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

    except pymysql.MySQLError as e:

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

def sjrImport():

    path = (Path(__file__).resolve().parent / ARQUIVO_PATH_SJR).resolve()

    log()
    log("Importação SJR Scopus")
    log(f"Diretório: {path}")
    log()

    if not path.exists():
        return erro(f"Diretório não encontrado: {path}")

    files = sorted(path.glob("*.csv"))

    if not files:
        return erro(f"Nenhum arquivo encontrado em {path}")

    connection = None

    try:

        connection = get_connection("brapci_journals")

        with connection.cursor() as cursor:

            id_ranking_source = get_or_create_sjr(cursor)
            connection.commit()

            log("SJR Scopus: "
                f"id={id_ranking_source}")

            log(f"Arquivos encontrados: {len(files)}")

            total_all = 0
            created_all = 0
            rankings_all = 0
            errors_all = 0

            for filename in files:

                try:

                    total, created, rankings, errors = \
                        import_sjr_file(
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

    except pymysql.MySQLError as e:

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


def issnLImport():
    """
    Importa a tabela de equivalência ISSN -> ISSN-L.
    """

    path = (Path(__file__).resolve().parent / ARQUIVO_PATH_ISSNL).resolve()

    log()
    log("Importação ISSN-L")
    log(f"Arquivo: {path}")
    log()

    if not path.exists():
        return erro(f"Arquivo não encontrado: {path}")

    connection = None

    try:
        connection = get_connection("brapci_journals")

        with connection.cursor() as cursor:
            cursor.execute("TRUNCATE TABLE issn_l")

            sql = """
                INSERT INTO issn_l
                (
                    issn,
                    issn_l,
                    type
                )
                VALUES (%s, %s, %s)
            """

            total = 0
            imported = 0
            errors = 0
            ignored_same = 0

            with open(path, "r", encoding="utf-8-sig", newline="") as f:
                for line_num, raw_line in enumerate(f, start=1):
                    line = raw_line.strip()

                    if not line:
                        continue

                    if line_num == 1 and "ISSN" in line.upper():
                        continue

                    total += 1

                    try:
                        parts = raw_line.rstrip("\r\n").split("\t")

                        if len(parts) < 2:
                            errors += 1
                            log(f"[IGNORADO] Linha {line_num}: formato inválido")
                            continue

                        issn = normalize_issn(parts[0])
                        issn_l = normalize_issn(parts[1])
                        type_value = parse_issnl_type(parts[2] if len(parts) > 2
                                                      else "ta")

                        if not issn or not issn_l:
                            errors += 1
                            log(f"[IGNORADO] Linha {line_num}: ISSN inválido")
                            continue

                        if issn == issn_l:
                            ignored_same += 1
                            continue

                        cursor.execute(sql, (issn, issn_l, type_value))
                        imported += 1

                    except Exception as e:
                        errors += 1
                        log(f"[ERRO] Linha {line_num}: {e}")

            connection.commit()

            log("Importação ISSN-L concluída")
            log(f"Registros lidos:      {total}")
            log(f"Registros importados: {imported}")
            log(f"Ignorados (ISSN=ISSN-L): {ignored_same}")
            log(f"Erros/ignorados:      {errors}")

            return {
                "success": True,
                "records": total,
                "imported": imported,
                "ignored_same": ignored_same,
                "errors": errors,
            }

    except pymysql.MySQLError as e:

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


def checkJournalRDF():
    """
    Relaciona os periódicos da base BRAPCI às publicações pelo ISSN-L
    e grava o identificador RDF da fonte em publications.rdf_id.
    """

    connection = None

    try:
        connection = get_connection("brapci_journals")

        with connection.cursor() as cursor:
            cursor.execute("""
                SELECT id_jnl, jnl_name, jnl_issn, jnl_eissn, jnl_frbr
                FROM brapci.source_source
            """)
            sources = cursor.fetchall()

            cursor.execute("SELECT issn, issn_l FROM issn_l")
            issn_l_map = {}
            for row in cursor.fetchall():
                issn = normalize_issn(row.get("issn"))
                issn_l = normalize_issn(row.get("issn_l"))
                if issn and issn_l:
                    issn_l_map[issn] = issn_l

            cursor.execute("""
                SELECT id_publication, issn_l, rdf_id
                FROM publications
            """)
            publications_by_issn_l = {}
            for row in cursor.fetchall():
                issn_l = normalize_issn(row.get("issn_l"))
                if issn_l:
                    publications_by_issn_l.setdefault(issn_l, []).append(row)

            matched = 0
            updated = 0
            unchanged = 0
            without_issn = 0
            without_rdf = 0
            not_found = []

            update_sql = """
                UPDATE publications
                SET rdf_id = %s
                WHERE id_publication = %s
            """

            for source in sources:
                rdf_id = source.get("jnl_frbr")
                if rdf_id is None or str(rdf_id).strip() in ("", "0"):
                    without_rdf += 1
                    continue

                source_issns = []
                for field in ("jnl_issn", "jnl_eissn"):
                    issn = normalize_issn(source.get(field))
                    if issn:
                        issn_l = issn_l_map.get(issn, issn)
                        if issn_l not in source_issns:
                            source_issns.append(issn_l)

                if not source_issns:
                    without_issn += 1
                    continue

                publications = {}
                for issn_l in source_issns:
                    for publication in publications_by_issn_l.get(issn_l, []):
                        publications[publication["id_publication"]] = publication

                if not publications:
                    item = {
                        "id_jnl": source.get("id_jnl"),
                        "journal": source.get("jnl_name"),
                        "jnl_issn": source.get("jnl_issn"),
                        "jnl_eissn": source.get("jnl_eissn"),
                        "issn_l": source_issns,
                        "jnl_frbr": rdf_id,
                        "status": "not_found",
                    }
                    not_found.append(item)
                    log(
                        f"[NOT FOUND] id_jnl={item['id_jnl']} | "
                        f"ISSN-L={', '.join(source_issns)} | "
                        f"RDF={rdf_id} | {item['journal'] or ''}"
                    )
                    continue

                for publication in publications.values():
                    matched += 1
                    if str(publication.get("rdf_id") or "") == str(rdf_id):
                        unchanged += 1
                        continue

                    cursor.execute(update_sql, (
                        rdf_id,
                        publication["id_publication"],
                    ))
                    publication["rdf_id"] = rdf_id
                    updated += 1

            connection.commit()

            log("Vinculação RDF dos periódicos concluída")
            log(f"Fontes lidas:            {len(sources)}")
            log(f"Publicações localizadas: {matched}")
            log(f"Publicações atualizadas: {updated}")
            log(f"Já atualizadas:          {unchanged}")
            log(f"Sem ISSN válido:         {without_issn}")
            log(f"Sem RDF:                 {without_rdf}")
            log(f"Sem correspondência:     {len(not_found)}")

            return {
                "success": True,
                "sources": len(sources),
                "matched": matched,
                "updated": updated,
                "unchanged": unchanged,
                "without_issn": without_issn,
                "without_rdf": without_rdf,
                "not_found_count": len(not_found),
                "not_found": not_found,
            }

    except Exception as e:
        if connection:
            connection.rollback()
        return erro(str(e))

    finally:
        if connection:
            connection.close()


def truncate_publication_tables():
    """
    Zera as tabelas relacionadas às publicações.

    Remove todos os registros e reinicia os AUTO_INCREMENT.
    """

    tables = [
        "publication_rankings", "publication_publishers", "publication_issns",
        "publications","issn_l"
    ]

    connection = None

    try:
        connection = get_connection("brapci_journals")

        with connection.cursor() as cursor:

            # Desabilita temporariamente as verificações de FK
            cursor.execute("SET FOREIGN_KEY_CHECKS = 0")

            for table in tables:
                log(f"Zerando tabela: {table}")
                cursor.execute(f"TRUNCATE TABLE `{table}`")

            # Reabilita as verificações de FK
            cursor.execute("SET FOREIGN_KEY_CHECKS = 1")

        connection.commit()

        log("Tabelas zeradas com sucesso.")

        return {
            "success": True,
            "tables": tables,
            "message": "Tabelas zeradas com sucesso."
        }

    except Exception as e:

        if connection:
            connection.rollback()

            # Garante que FOREIGN_KEY_CHECKS seja reativado
            try:
                with connection.cursor() as cursor:
                    cursor.execute("SET FOREIGN_KEY_CHECKS = 1")
            except Exception:
                pass

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

    if action == "sjr":
        return sjrImport()

    if action == "issn-l":
        return issnLImport()

    if action == "zera":
        return truncate_publication_tables()

    if action == "check":
        return checkJournalRDF()


    if action == "status":
        return {"success": True, "task": TASK["name"], "status": "ready"}

    return erro(f"Ação desconhecida: {action}")


if __name__ == "__main__":

    resultado = run(parametros=sys.argv[2:], silent=False)

    print()
    print(resultado)
