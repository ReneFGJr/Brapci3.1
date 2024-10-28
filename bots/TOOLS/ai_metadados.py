import re

def extrair_metodologia(texto):
    Tini = ''
    Tfim = ''
    if 'PROCEDIMENTOS METODOLÓGICOS' in texto:
        Tini = 'PROCEDIMENTOS METODOLÓGICOS'
    if 'METODOLOGIA' in texto:
        Tini = 'PROCEDIMENTOS METODOLÓGICOS'

def locale_referencias_type(text):
    tp = ['REFERÊNCIAS']

    for i in tp:
        ti = i + chr(10)
        if ti in text:
            return i.strip()
    return ""

def extrair_referencias(texto):
    start_section = locale_referencias_type(texto)
    print("SECTION",start_section)
    if start_section != '':

    # Encontrar o índice da seção de referências
        start_index = None
        lines = texto.splitlines()
        for i, line in enumerate(lines):
            if start_section in line.upper():
                start_index = i
                break

        # Verifica se a seção foi encontrada
        if start_index is None:
            return "Seção de referências não encontrada."

        # Extrair a partir do índice encontrado até o final do arquivo
        references = "".join(texto[start_index:]).strip()
        return references
    return "none"

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
