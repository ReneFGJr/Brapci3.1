import database
import re
from urllib.parse import unquote

def encontrar_doi(texto):
    # Expressão regular para identificar o padrão de DOI
    texto = unquote(texto)
    padrao_doi = r'\b(10\.\d{4,9}/[-._;()/:A-Z0-9]+)\b'

    # Procurar todos os DOI's no texto
    texto = str(texto)
    doi_encontrados = re.findall(padrao_doi, texto, re.IGNORECASE)

    if doi_encontrados:
        doi_encontrados = doi_encontrados[0]
    if doi_encontrados == []:
        doi_encontrados = '-'
    return doi_encontrados
