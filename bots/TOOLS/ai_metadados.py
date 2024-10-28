import re

def extrair_secoes_method_01(texto):
    secoes = {
        "Título": re.search(r"Artigo\s+(.*)", texto, re.IGNORECASE),
        "Palavras-chave": re.search(r"PALAVRAS-CHAVE\s*(.*)", texto, re.IGNORECASE),
        "Resumo": re.search(r"RESUMO\s*(.*?)(?=\nPALAVRAS-CHAVE)", texto, re.IGNORECASE | re.DOTALL),
        "Introdução": re.search(r"INTRODUÇÃO\s*(.*?)(?=\nPROCEDIMENTOS METODOLÓGICOS)", texto, re.IGNORECASE | re.DOTALL),
        "Metodologia": re.search(r"PROCEDIMENTOS METODOLÓGICOS\s*(.*?)(?=\nRESULTADOS)", texto, re.IGNORECASE | re.DOTALL),
        "Resultados": re.search(r"RESULTADOS\s*(.*?)(?=\nCONCLUSÃO)", texto, re.IGNORECASE | re.DOTALL),
        "Conclusão": re.search(r"CONCLUSÃO\s*(.*?)(?=\nREFERÊNCIAS)", texto, re.IGNORECASE | re.DOTALL),
        "Referencial Teórico": re.search(r"REFERENCIAL TEÓRICO\s*(.*?)(?=\nMETODOLOGIA)", texto, re.IGNORECASE | re.DOTALL)
    }

    extracoes = {}
    for secao, conteudo in secoes.items():
        if conteudo:
            extracoes[secao] = conteudo.group(1).strip()
        else:
            extracoes[secao] = None

    return extracoes
