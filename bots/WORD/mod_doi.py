import re

def locate(text):

    # Padrão regex para identificar DOIs
    doi_pattern = re.compile(r'\b10.\d{4,9}/[-._;()/:A-Z0-9]+\b', re.IGNORECASE)

    # Encontrar todos os DOIs no texto
    dois = re.findall(doi_pattern, text)

    return dois