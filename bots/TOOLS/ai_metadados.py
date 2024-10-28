import re

def extrair_secoes_method_01(texto):
    secoes = {
        "Título": re.search(r"(?<=Artigo\s+)(.*)", texto, re.IGNORECASE),
        "Palavras-chave": re.search(r"(?<=PALAVRAS-CHAVE)(.*)", texto, re.IGNORECASE),
        "Resumo": re.search(r"(?<=RESUMO)(.*?)(?=INTRODUÇÃO)", texto, re.IGNORECASE | re.DOTALL),
        "Introdução": re.search(r"(?<=INTRODUÇÃO)(.*?)(?=PROCEDIMENTOS METODOLÓGICOS)", texto, re.IGNORECASE | re.DOTALL),
        "Metodologia": re.search(r"(?<=PROCEDIMENTOS METODOLÓGICOS)(.*?)(?=RESULTADOS)", texto, re.IGNORECASE | re.DOTALL),
        "Resultados": re.search(r"(?<=RESULTADOS)(.*?)(?=CONCLUSÃO)", texto, re.IGNORECASE | re.DOTALL),
        "Conclusão": re.search(r"(?<=CONCLUSÃO)(.*?)(?=REFERÊNCIAS)", texto, re.IGNORECASE | re.DOTALL),
        "Referencial Teórico": re.search(r"(?<=REFERENCIAL TEÓRICO)(.*?)(?=METODOLOGIA)", texto, re.IGNORECASE | re.DOTALL)
    }

    extracoes = {}
    for secao, conteudo in secoes.items():
        if conteudo:
            extracoes[secao] = conteudo.group().strip()
        else:
            extracoes[secao] = None

    return extracoes
