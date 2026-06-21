import re
import sys
import mod_api, ai_ollama_chat
from colorama import Fore

def locateKeywords(text):
    t = {
        'Palavras–chave:', 'Palavras-Chave:', 'Palavras-chave:',
        'Palavra-chave:', 'Palavra-Chave:',
        '## Palavras–chave:', '## Palavras-Chave:', '## Palavras-chave:',
        '## Palavra-chave:', '## Palavra-Chave:',
    }
    for te in t:
        if te in text:
            return te
    return ""

def locateAbstract(text):
    t = {'Abstract:','ABSTRACT:','Abstract','ABSTRACT','1 Introdução','1 INTRODUÇÃO'}
    for te in t:
        if te in text:
            return te
    return ""

def extract_keywords_ollama(text,id):
    rsp = ai_ollama_chat.extrair_palavras_chave(text)
    print("==>",rsp)

import re

def extract_keywords(text, ID=None):

    pattern = r'''
        Palavras[- ]?chave[s]?      # marcador
        \s*:?\s*
        (.*?)                       # conteúdo das palavras-chave
        (?=
            Abstract\s*:|
            Resumo\s*:|
            Introdução|
            Introduction|
            1\s+Introdução|
            1\s+Introduction|
            \n[A-Z][A-Za-z\s]{3,20}\:
        )
    '''

    match = re.search(
        pattern,
        text,
        re.IGNORECASE | re.DOTALL | re.VERBOSE
    )

    if not match:
        return []

    content = match.group(1).strip()

    keywords = re.split(r'[;,.]', content)

    indexKeyWords(keywords, ID)

    return [
        k.strip()
        for k in keywords
        if len(k.strip()) > 1
    ]

def indexKeyWords(keys,idR):
    urlKey = 'https://cip.brapci.inf.br/api/rdf/createConcept/Subject?lang=pt&name='
    tkey = 0
    stop = 0
    size = 80
    for k in keys:
            if (len(k) >= size):
                print("Termo muito longo: ",Fore.RED,len(k),k,Fore.WHITE)
                stop = 1
            if (len(k) <= 2):
                stop = 1

            if k != '' and len(k) < size and stop == 0 and tkey <= 6:
                print("==>Processando:",Fore.BLUE,k,Fore.WHITE)
                tkey = tkey + 1
                data = {'apikey': k}
                rst = mod_api.api_post(urlKey + k, data)

                try:
                    # Verificar se 'rst' é um dicionário e possui a chave 'id'
                    if isinstance(rst, dict) and 'id' in rst:
                        idr = rst['id']
                        url = f'https://cip.brapci.inf.br/api/rdf/dataAdd/?source={idR}&prop=hasSubject&resource={idr}'
                        rst = mod_api.api_post(url, data)
                    else:
                        print(f"Resposta inesperada da API para '{k}'")
                        print(f"==>{rst}")
                except Exception as e:
                    print("=================ERRO===========")
                    print(f"Erro ao processar a resposta da API para a palavra-chave '{k}': {e}")
                    print(f"URL: {url}")
            else:
                print(Fore.RED,"=nao processado=>",k,Fore.WHITE)
                print(k != '',len(k) < 40,stop == 0,tkey <= 6)