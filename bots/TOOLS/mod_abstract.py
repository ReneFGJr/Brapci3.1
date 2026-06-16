#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import json
import re
from collections import Counter

ARQUIVO_MD = "/data/Brapci3.1/public/_repository/00/13/50/06/work_00135006#00000.md"
ARQUIVO_JSON = "/data/Brapci3.1/public/_repository/00/13/50/06/work_00135006#00000.json"


def limpar_markdown(texto):
    """Remove marcações básicas de Markdown."""
    texto = re.sub(r'```.*?```', ' ', texto, flags=re.S)
    texto = re.sub(r'`.*?`', ' ', texto)
    texto = re.sub(r'!\[.*?\]\(.*?\)', ' ', texto)
    texto = re.sub(r'\[([^\]]+)\]\([^)]+\)', r'\1', texto)
    texto = re.sub(r'[#>*_\-\|]', ' ', texto)
    texto = re.sub(r'\s+', ' ', texto)
    return texto.strip()


def extrair_palavras_chave(texto, n=5):
    stopwords = {
        'a', 'o', 'e', 'de', 'da', 'do', 'das', 'dos', 'em', 'para',
        'por', 'com', 'um', 'uma', 'os', 'as', 'na', 'no', 'nas', 'nos',
        'que', 'se', 'ao', 'à', 'às', 'ou', 'como', 'mais', 'menos',
        'são', 'foi', 'ser', 'sua', 'seu', 'suas', 'seus', 'entre',
        'sobre', 'também', 'este', 'esta', 'esses', 'essas', 'artigo',
        'pesquisa', 'estudo', 'trabalho'
    }

    palavras = re.findall(r'\b[\wÀ-ÿ]{4,}\b', texto.lower())

    palavras = [
        p for p in palavras
        if p not in stopwords and not p.isdigit()
    ]

    frequencia = Counter(palavras)

    return [p for p, _ in frequencia.most_common(n)]


def resumir_texto(texto, limite_palavras=300):
    frases = re.split(r'(?<=[.!?])\s+', texto)

    resumo = []
    total = 0

    for frase in frases:
        qtd = len(frase.split())

        if total + qtd > limite_palavras:
            break

        resumo.append(frase)
        total += qtd

    return " ".join(resumo)


def main():

    with open(ARQUIVO_MD, "r", encoding="utf-8") as f:
        md = f.read()

    texto = limpar_markdown(md)

    resumo = resumir_texto(texto, 300)
    palavras_chave = extrair_palavras_chave(texto, 5)

    resultado = {
        "arquivo": ARQUIVO_MD,
        "resumo": resumo,
        "palavras_chave": palavras_chave,
        "total_palavras_resumo": len(resumo.split())
    }

    with open(ARQUIVO_JSON, "w", encoding="utf-8") as f:
        json.dump(resultado, f, ensure_ascii=False, indent=4)

    print(f"JSON gerado: {ARQUIVO_JSON}")


if __name__ == "__main__":
    main()