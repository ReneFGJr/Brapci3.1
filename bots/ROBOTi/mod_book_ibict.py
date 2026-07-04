#https://omp-editora.prd.ibict.br/index.php/edibict/oai?verb=ListRecords&metadataPrefix=oai_dc
#https://omp-editora.prd.ibict.br/index.php/edibict/catalog/book/277
#pip install requests pymysql lxml

import json
import requests
import pymysql
import re
import env
from lxml import etree

URL = "https://omp-editora.prd.ibict.br/index.php/edibict/oai?verb=ListRecords&metadataPrefix=oai_dc"
MYSQL = {**env.db(), "database": "brapci_books"}

NS = {
    "oai": "http://www.openarchives.org/OAI/2.0/",
    "oai_dc": "http://www.openarchives.org/OAI/2.0/oai_dc/",
    "dc": "http://purl.org/dc/elements/1.1/"
}


def get_text(node, xpath):
    x = node.find(xpath, NS)
    if x is None:
        return None
    if x.text:
        return x.text.strip()
    return None


def get_list(node, xpath):
    return [i.text.strip() for i in node.findall(xpath, NS) if i.text]


def normalize_isbn(value):
    if not value:
        return None

    isbn = value.replace("-", "").replace(".", "").strip()
    isbn = re.sub(r"\s+", "", isbn)

    if isbn.startswith("978"):
        return isbn
    return None


def extract_isbn(identifiers):
    for item in identifiers:
        isbn = normalize_isbn(item)
        if isbn:
            return isbn
    return None


def normalize_doi(value):
    if not value:
        return None

    doi = value.strip()
    doi = re.sub(r"^https?://(dx\.)?doi\.org/", "", doi, flags=re.IGNORECASE)

    match = re.search(r"10\.\d{4,9}/\S+", doi)
    if not match:
        return None

    doi = match.group(0).strip().rstrip(".,;)")
    return doi


def extract_catalog_url(identifiers_field):
    if not identifiers_field:
        return None

    values = []
    if isinstance(identifiers_field, str):
        try:
            parsed = json.loads(identifiers_field)
            if isinstance(parsed, list):
                values = parsed
            elif isinstance(parsed, str):
                values = [parsed]
        except Exception:
            values = [identifiers_field]
    elif isinstance(identifiers_field, list):
        values = identifiers_field

    urls = [v.strip() for v in values if isinstance(v, str) and v.strip().startswith("http")]

    for u in urls:
        if "/catalog/book/" in u:
            return u

    return urls[0] if urls else None


def fetch_cover_and_doi(page_url):
    if not page_url:
        return None, None

    try:
        response = requests.get(page_url, timeout=60)
        response.raise_for_status()
    except requests.RequestException:
        return None, None

    root = etree.HTML(response.content)
    if root is None:
        return None, None

    cover = None
    doi = None

    cover_candidates = root.xpath("//div[contains(@class,'item cover')]//img/@src")
    if not cover_candidates:
        cover_candidates = root.xpath("//meta[@property='og:image']/@content")
    if cover_candidates:
        cover = cover_candidates[0].strip()

    doi_candidates = root.xpath("//meta[@name='citation_doi']/@content")
    if not doi_candidates:
        doi_candidates = root.xpath("//meta[@name='DC.Identifier.DOI']/@content")
    if not doi_candidates:
        doi_candidates = root.xpath("//div[contains(@class,'item doi')]//a/text()")

    for candidate in doi_candidates:
        doi = normalize_doi(candidate)
        if doi:
            break

    return cover, doi


def fetch_book_chapters(page_url):
    if not page_url:
        return []

    try:
        response = requests.get(page_url, timeout=60)
        response.raise_for_status()
    except requests.RequestException:
        return []

    root = etree.HTML(response.content)
    if root is None:
        return []

    chapters = []
    items = root.xpath("//div[contains(@class,'item chapters')]//ul/li")

    for item in items:
        title = item.xpath("normalize-space(.//div[contains(@class,'title')])")

        author = item.xpath("normalize-space(.//div[contains(@class,'authors')])")
        if not author:
            author = item.xpath("normalize-space(.//div[contains(@class,'subtitle')])")

        doi_candidates = item.xpath(
            ".//div[contains(@class,'doi')]//a/@href"
            " | .//div[contains(@class,'doi')]//a/text()"
            " | .//div[contains(@class,'doi')]/text()"
        )
        doi = None
        for candidate in doi_candidates:
            doi = normalize_doi(candidate)
            if doi:
                break

        chapter = {
            "title": title if title else None,
            "author": author if author else None,
            "doi": doi,
        }

        if chapter["title"] or chapter["author"] or chapter["doi"]:
            chapters.append(chapter)

    return chapters


def enrich_chapterbook(limit=None):
    conn = pymysql.connect(**MYSQL)
    cur = conn.cursor()

    sql = """
    SELECT identifier, identifiers
    FROM brapci_books.book_harvesting
    WHERE `status` = 1
    """
    if limit is not None:
        sql += " LIMIT %s"
        cur.execute(sql, (int(limit),))
    else:
        cur.execute(sql)

    rows = cur.fetchall()
    processed = 0
    updated = 0
    failed = 0

    for identifier, identifiers_field in rows:
        processed += 1
        print(f"processando capitulos {processed}: {identifier}")

        try:
            page_url = extract_catalog_url(identifiers_field)
            chapters = fetch_book_chapters(page_url)
            chapters_json = json.dumps(chapters, ensure_ascii=False)

            cur.execute(
                """
                UPDATE brapci_books.book_harvesting
                SET ChaptherBook = %s,
                    `status` = 2
                WHERE identifier = %s
                """,
                (chapters_json, identifier),
            )

            if cur.rowcount > 0:
                updated += 1

        except Exception as e:
            failed += 1
            print(f"erro em capitulos {identifier}: {e}")
            cur.execute(
                """
                UPDATE brapci_books.book_harvesting
                SET `status` = 9
                WHERE identifier = %s
                """,
                (identifier,),
            )

        if processed % 50 == 0:
            conn.commit()
            print(f"capitulos processados={processed} atualizados={updated} erros={failed}")

    conn.commit()
    conn.close()

    print(f"capitulos finalizado: processados={processed} atualizados={updated} erros={failed}")


def enrich_cover_and_doi(limit=None):
    conn = pymysql.connect(**MYSQL)
    cur = conn.cursor()

    sql = """
    SELECT identifier, identifiers, coverage, DOI
    FROM brapci_books.book_harvesting
    WHERE `status` = 0
    """
    if limit is not None:
        sql += " LIMIT %s"
        cur.execute(sql, (int(limit),))
    else:
        cur.execute(sql)

    rows = cur.fetchall()
    processed = 0
    updated = 0
    failed = 0

    for identifier, identifiers_field, coverage, doi in rows:
        processed += 1
        print(f"processando {processed}: {identifier}")

        try:
            page_url = extract_catalog_url(identifiers_field)
            new_cover = None
            new_doi = None

            if page_url:
                new_cover, new_doi = fetch_cover_and_doi(page_url)

            final_cover = new_cover if new_cover else coverage
            final_doi = new_doi if new_doi else doi

            cur.execute(
                """
                UPDATE brapci_books.book_harvesting
                SET coverage = %s,
                    DOI = %s,
                    `status` = 1
                WHERE identifier = %s
                """,
                (final_cover, final_doi, identifier),
            )

            if cur.rowcount > 0:
                updated += 1

        except Exception as e:
            failed += 1
            print(f"erro em {identifier}: {e}")
            cur.execute(
                """
                UPDATE brapci_books.book_harvesting
                SET `status` = 9
                WHERE identifier = %s
                """,
                (identifier,),
            )

        if processed % 50 == 0:
            conn.commit()
            print(f"processados={processed} atualizados={updated} erros={failed}")

    conn.commit()
    conn.close()

    print(f"enriquecimento finalizado: processados={processed} atualizados={updated} erros={failed}")


def save_record(cur, record):

    sql = """
    INSERT INTO brapci_books.book_harvesting
    (
        identifier,
        datestamp,
        setSpec,
        title,
        isbn,
        creators,
        subjects,
        description,
        publishers,
        contributors,
        dc_date,
        dc_type,
        format,
        identifiers,
        source,
        language,
        relation,
        coverage,
        rights,
        raw_xml
    )
    VALUES
    (
        %(identifier)s,
        %(datestamp)s,
        %(setSpec)s,
        %(title)s,
        %(isbn)s,
        %(creators)s,
        %(subjects)s,
        %(description)s,
        %(publishers)s,
        %(contributors)s,
        %(dc_date)s,
        %(dc_type)s,
        %(format)s,
        %(identifiers)s,
        %(source)s,
        %(language)s,
        %(relation)s,
        %(coverage)s,
        %(rights)s,
        %(raw_xml)s
    )
    ON DUPLICATE KEY UPDATE

        datestamp=VALUES(datestamp),
        setSpec=VALUES(setSpec),
        title=VALUES(title),
        isbn=VALUES(isbn),
        creators=VALUES(creators),
        subjects=VALUES(subjects),
        description=VALUES(description),
        publishers=VALUES(publishers),
        contributors=VALUES(contributors),
        dc_date=VALUES(dc_date),
        dc_type=VALUES(dc_type),
        format=VALUES(format),
        identifiers=VALUES(identifiers),
        source=VALUES(source),
        language=VALUES(language),
        relation=VALUES(relation),
        coverage=VALUES(coverage),
        rights=VALUES(rights),
        raw_xml=VALUES(raw_xml)
    """

    cur.execute(sql, record)


def title_exists(cur, title):
    if not title:
        return False

    sql = """
    SELECT 1
    FROM brapci_books.book_harvesting
    WHERE title = %s
    LIMIT 1
    """
    cur.execute(sql, (title,))
    return cur.fetchone() is not None


def harvest():

    conn = pymysql.connect(**MYSQL)
    cur = conn.cursor()

    url = URL

    total = 0

    while url:

        print(url)

        xml = requests.get(url, timeout=120).content

        root = etree.fromstring(xml)

        records = root.findall(".//oai:record", NS)

        for rec in records:

            header = rec.find("oai:header", NS)

            if header is not None and header.get("status") == "deleted":
                continue

            meta = rec.find("oai:metadata", NS)

            if meta is None:
                meta_nodes = rec.xpath("./*[local-name()='metadata']")
                meta = meta_nodes[0] if meta_nodes else None

            if meta is None:
                continue

            dc = meta.find("oai_dc:dc", NS)

            if dc is None:
                dc_nodes = meta.xpath("./*[local-name()='dc']")
                dc = dc_nodes[0] if dc_nodes else None

            if dc is None:
                continue

            row = {
                "_identifiers_list":
                get_list(dc, "dc:identifier"),
                "identifier":
                get_text(header, "oai:identifier"),
                "datestamp":
                get_text(header, "oai:datestamp"),
                "setSpec":
                get_text(header, "oai:setSpec"),
                "title":
                get_text(dc, "dc:title"),
                "isbn":
                None,
                "creators":
                json.dumps(get_list(dc, "dc:creator"), ensure_ascii=False),
                "subjects":
                json.dumps(get_list(dc, "dc:subject"), ensure_ascii=False),
                "description":
                get_text(dc, "dc:description"),
                "publishers":
                json.dumps(get_list(dc, "dc:publisher"), ensure_ascii=False),
                "contributors":
                json.dumps(get_list(dc, "dc:contributor"), ensure_ascii=False),
                "dc_date":
                get_text(dc, "dc:date"),
                "dc_type":
                get_text(dc, "dc:type"),
                "format":
                get_text(dc, "dc:format"),
                "identifiers":
                None,
                "source":
                get_text(dc, "dc:source"),
                "language":
                get_text(dc, "dc:language"),
                "relation":
                get_text(dc, "dc:relation"),
                "coverage":
                get_text(dc, "dc:coverage"),
                "rights":
                get_text(dc, "dc:rights"),
                "raw_xml":
                etree.tostring(rec, encoding="unicode", pretty_print=True)
            }

            row["isbn"] = extract_isbn(row["_identifiers_list"])
            row["identifiers"] = json.dumps(row["_identifiers_list"], ensure_ascii=False)
            del row["_identifiers_list"]

            if title_exists(cur, row["title"]):
                continue

            save_record(cur, row)

            total += 1

            if total % 100 == 0:
                conn.commit()
                print(total)

        conn.commit()

        token = root.find(".//oai:resumptionToken", NS)

        if token is None or token.text is None or token.text.strip() == "":
            break

        url = "https://omp-editora.prd.ibict.br/index.php/edibict/oai?verb=ListRecords&resumptionToken=" + token.text.strip(
        )

    conn.commit()
    conn.close()

    print(f"{total} registros coletados.")


if __name__ == "__main__":
    enrich_chapterbook()
    ##enrich_cover_and_doi()
    ##harvest()
