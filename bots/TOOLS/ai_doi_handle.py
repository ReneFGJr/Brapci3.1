import re

def extrair_doi(linha):
    # Expressão regular para capturar DOI (formato básico)
    padrao_doi = r'10\.\d{4,9}/[-._;()/:A-Za-z0-9]+'
    dois = []

    dois.extend(re.findall(padrao_doi, linha))
    return dois

def extrair_handle(linha):
    # Expressão regular para capturar HANDLE (geralmente no formato "prefixo/sufixo")
    padrao_handle = r'handle.net/\d+/\d+'
    handles = []
    # Procura todos os HANDLES na linha
    handles.extend(re.findall(padrao_handle, linha))

    return handles