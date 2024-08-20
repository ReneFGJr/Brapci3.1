import database
import re

def encontrar_doi(texto):
    # Expressão regular para identificar o padrão de DOI
    padrao_doi = r'\b(10\.\d{4,9}/[-._;()/:A-Z0-9]+)\b'

    # Procurar todos os DOI's no texto
    doi_encontrados = re.findall(padrao_doi, texto, re.IGNORECASE)

    return doi_encontrados

def locate():
    qr = "select * from brapci_cited.cited_article "
    qr += " where ca_doi = '' "
    qr += " and ca_text like '%10.%' "
    qr += "limit 10 "
    row = database.query(qr)

    for line in row:
        print(line[12])
        DOI = encontrar_doi(line[12])
        print("DOI",DOI)
