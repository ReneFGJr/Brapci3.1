import re
import sys
import mod_rdf

def doi(linha, ID):
    """
    Extrai DOIs de uma linha de texto.
    :param linha: Linha de texto que pode conter DOIs.
    :return: Lista de DOIs encontrados.
    """
    dois = extrair_doi(linha)
    if (len(dois) > 0):
        print(dois[0])
        DOI = dois[0]
        Class = 45
        Prop = 75
        mod_rdf.rdf_insert_concept(Class, DOI, Prop, ID)

    sys.exit(0)
    return dois

def extrair_doi(linha):
    # Expressão regular para capturar DOI (formato básico)
    padrao_doi = r'10\.\d{4,9}/[-._;()/:A-Za-z0-9]+'
    dois = []

    dois.extend(re.findall(padrao_doi, linha))
    return dois

def extrair_handle(linha):
    # Expressão regular para capturar HANDLE (geralmente no formato "prefixo/sufixo")
    padrao_handle = r'hdl.handle.net/\d+/\d+'
    handles = []
    # Procura todos os HANDLES na linha
    handles.extend(re.findall(padrao_handle, linha))

    return handles
