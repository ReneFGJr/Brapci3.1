import re
import sys_io
import sys

def extrair_metodologia(texto):
    Tini = ''
    Tfim = ''
    if 'PROCEDIMENTOS METODOLÓGICOS' in texto:
        Tini = 'PROCEDIMENTOS METODOLÓGICOS'
    if 'METODOLOGIA' in texto:
        Tini = 'PROCEDIMENTOS METODOLÓGICOS'

def locale_referencias_type(text):
    tp = ['REFERÊNCIAS']

    # Divide o texto em linhas
    linhas = sys_io.separar_por_linhas(text)

    # Percorre cada linha
    for linha in linhas:
        for wd in tp:
            # Verifica se a palavra-chave está na linha
            if wd in linha:
                return wd.strip()
    sys.exit()
    return ""

def extrair_referencias(texto):
    start_section = locale_referencias_type(texto)
    ref = ""
    ref_On = False

    # Divide o texto em linhas
    linhas = sys_io.separar_por_linhas(texto)

    # Percorre cada linha
    for linha in linhas:

        if not ref_On:
            if start_section in linha:
                ref_On = True
                print("==",linha)
        else:
            ref = ref + linha + '#' + chr(13)
    return ref

def extrair_secoes_method_01(texto):
    secoes = {
        "Título": re.search(r"Artigo\s+(.*)", texto, re.IGNORECASE),
        "Palavras-chave": re.search(r"PALAVRAS-CHAVE\s*(.*)", texto, re.IGNORECASE),
        "Resumo": re.search(r"RESUMO\s*(.*?)(?=\nPALAVRAS-CHAVE)", texto, re.IGNORECASE | re.DOTALL),
        "Introdução": re.search(r"INTRODUÇÃO\s*(.*?)(?=\nPROCEDIMENTOS METODOLÓGICOS)", texto, re.IGNORECASE | re.DOTALL),
        "Metodologia": re.search(r"PROCEDIMENTOS METODOLÓGICOS\s*(.*?)(?=\nRESULTADOS)", texto, re.IGNORECASE | re.DOTALL),
        "Resultados": re.search(r"RESULTADOS\s*(.*?)(?=\nCONCLUSÃO)", texto, re.IGNORECASE | re.DOTALL),
        "Conclusão": re.search(r"CONCLUSÃO\s*(.*?)(?=\nREFERÊNCIAS)", texto, re.IGNORECASE | re.DOTALL),
        "ReferencialTeórico": re.search(r"REFERENCIAL TEÓRICO\s*(.*?)(?=\nMETODOLOGIA)", texto, re.IGNORECASE | re.DOTALL)
    }

    extracoes = {}
    for secao, conteudo in secoes.items():
        if conteudo:
            extracoes[secao] = conteudo.group(1).strip()
        else:
            extracoes[secao] = None

    return extracoes
