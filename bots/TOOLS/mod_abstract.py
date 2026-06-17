#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import json
import re, sys
import os
import requests
import mod_docling
import ai_abstract
import ai_keywords
from pathlib import Path

# Configuração do Ollama
OLLAMA_URL = "http://localhost:11434/api/generate"
OLLAMA_MODEL = "llama3.2:3b"


def limpar_markdown(texto):
    """
    Remove marcações Markdown e reduz ruído para o LLM.
    """

    # Blocos de código
    texto = re.sub(r'```.*?```', ' ', texto, flags=re.S)

    # Código inline
    texto = re.sub(r'`[^`]*`', ' ', texto)

    # Imagens
    texto = re.sub(r'!\[.*?\]\(.*?\)', ' ', texto)

    # Links
    texto = re.sub(r'\[([^\]]+)\]\([^)]+\)', r'\1', texto)

    # Cabeçalhos
    texto = re.sub(r'^#+\s*', '', texto, flags=re.M)

    # Tabelas e símbolos markdown
    texto = re.sub(r'[\|\*\_\~\>]', ' ', texto)

    # Espaços duplicados
    texto = re.sub(r'\s+', ' ', texto)

    return texto.strip()


def resumir_com_ollama(texto):
    """
    Envia o texto ao Ollama e solicita um JSON contendo:
    - resumo (~300 palavras)
    - 5 palavras-chave
    """

    prompt = f"""
Você é um pesquisador especializado em Ciência da Informação.

Analise o texto abaixo e execute as tarefas:

1. Produza um resumo científico entre 350 e 550 palavras.
2. Identifique exatamente 5 palavras-chave, seja mais específico possível.
3. Utilize apenas informações presentes no texto.
4. Não invente informações.
5. Responda EXCLUSIVAMENTE em JSON válido.

Formato obrigatório:

{{
    "resumo": "texto do resumo",
    "palavras_chave": [
        "palavra1",
        "palavra2",
        "palavra3",
        "palavra4",
        "palavra5"
    ]
}}

Texto:

{texto[:30000]}
"""

    payload = {
        "model": OLLAMA_MODEL,
        "prompt": prompt,
        "stream": False,
        "format": "json",
        "options": {
            "temperature": 0,
            "top_p": 0.1,
            "num_ctx": 8192
        }
    }

    response = requests.post(
        OLLAMA_URL,
        json=payload,
        timeout=600
    )

    response.raise_for_status()

    resposta = response.json()["response"].strip()

    try:
        return json.loads(resposta)

    except Exception as e:
        print(f"Erro ao interpretar JSON do Ollama: {e}")

        return {
            "resumo": resposta,
            "palavras_chave": []
        }


def processar_markdown(arquivo_md):
    """
    Lê o markdown, gera resumo e palavras-chave.
    """

    with open(arquivo_md, "r", encoding="utf-8") as f:
        markdown = f.read()

    texto = limpar_markdown(markdown)

    resultado_llm = resumir_com_ollama(texto)

    resultado = {
        "arquivo": arquivo_md,
        "modelo": OLLAMA_MODEL,
        "resumo": resultado_llm.get("resumo", ""),
        "palavras_chave": resultado_llm.get("palavras_chave", [])
    }

    return resultado


def salvar_json(dados, arquivo_json):

    with open(arquivo_json, "w", encoding="utf-8") as f:
        json.dump(
            dados,
            f,
            ensure_ascii=False,
            indent=4
        )


def main(ID):
    ### Arquivo
    source_path = Path(f"work_{str(ID).zfill(8)}#00000.pdf")
    arquivo_md = mod_docling.build_repository_filename(ID, source_path)
    arquivo_js = arquivo_md.with_suffix(".json")
    print("=== arquivo_js:", arquivo_js)
    if (not arquivo_js.exists()):
        if not arquivo_md.exists():
            print(f"Arquivo não encontrado: {arquivo_md}")
            mod_docling.save_file_docling(ID, str(source_path))
            return

        print(f"Lendo: {arquivo_md}")
        resultado = processar_markdown(str(arquivo_md))
        salvar_json(resultado, arquivo_js)
    else:
        print(f"JSON já existe: {arquivo_js}")
        with open(arquivo_js, "r", encoding="utf-8") as f:
            resultado = json.load(f)

    abstract = resultado.get("resumo", "")
    palavras_chave = resultado.get("palavras_chave", [])
    print("Keywords:", palavras_chave)
    ai_keywords.indexKeyWords(palavras_chave, ID)

    if (abstract):
        ai_abstract.saveAbstract(ID, abstract)


    print("\nPalavras-chave:")
    for p in resultado["palavras_chave"]:
        print(f" - {p}")


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Uso:\n"
              "python mod_abstract.py <doc_id>")
        sys.exit(1)
    try:
        print(sys.argv[1])
        result = main(sys.argv[1])
    except ValueError:
        print("Erro: doc_id precisa ser numérico")
        sys.exit(1)
