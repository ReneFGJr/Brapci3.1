#https://omp-editora.prd.ibict.br/index.php/edibict/oai?verb=ListRecords&metadataPrefix=oai_dc
#https://omp-editora.prd.ibict.br/index.php/edibict/catalog/book/277
#pip install requests pymysql lxml

import json
import requests
import pymysql
import database
import env
from lxml import etree

URL = "https://omp-editora.prd.ibict.br/index.php/edibict/oai?verb=ListRecords&metadataPrefix=oai_dc"
MYSQL = env.db()

NS = {
    "oai": "http://www.openarchives.org/OAI/2.0/",
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


def save_record(cur, record):

    sql = """
    INSERT INTO book_harvesting
    (
        identifier,
        datestamp,
        setSpec,
        title,
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

    database.update(sql, record)


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
            meta = rec.find("oai:metadata", NS)

            if meta is None:
                continue

            dc = meta.find("dc:dc", NS)

            if dc is None:
                continue

            row = {
                "identifier":
                get_text(header, "oai:identifier"),
                "datestamp":
                get_text(header, "oai:datestamp"),
                "setSpec":
                get_text(header, "oai:setSpec"),
                "title":
                get_text(dc, "dc:title"),
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
                json.dumps(get_list(dc, "dc:identifier"), ensure_ascii=False),
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
    harvest()
