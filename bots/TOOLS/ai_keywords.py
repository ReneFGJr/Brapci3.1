import re
import sys
import mod_api

def locateKeywords(text):
    t = {'Palavras–chave:','Palavras-Chave:','Palavras-chave:'}
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

def extract_keywords(text,id):
    text = text.replace(chr(10), ' ')
    text = text.replace('.', ';')
    text = text.replace('Palavras-Chave','Palavras-chave')
    text = text.replace('Palavras Chave','Palavras-chave')
    term = locateAbstract(text)
    keyw = locateKeywords(text)

    if term == '' or keyw == '':
        print(f"Área não localizada [{keyw}],[{term}]")
        print(text[:2000])
        sys.exit()
    else:
        print("======>",keyw,term)

    exp = f"{keyw}\s*(.*?)(?={term})"
    match = re.search(exp, text, re.DOTALL)
    stop = 0

    if match:
        keywords = match.group(1).split(";")
        keys = [keyword.strip().capitalize() for keyword in keywords if keyword.strip()]
        urlKey = 'https://cip.brapci.inf.br/api/rdf/createConcept/Subject?lang=pt&name='
        for k in keys:
            if (len(k) >= 40):
                stop = 1

            if k != '' and len(k) < 40 and stop == 1:
                data = {'apikey': k}
                rst = mod_api.api_post(urlKey + k, data)

                try:
                    # Verificar se 'rst' é um dicionário e possui a chave 'id'
                    if isinstance(rst, dict) and 'id' in rst:
                        idr = rst['id']
                        print("==>", rst)
                        url = f'https://cip.brapci.inf.br/api/rdf/dataAdd/?source={id}&prop=hasSubject&resource={idr}'
                        rst = mod_api.api_post(url, data)
                    else:
                        print(f"Resposta inesperada da API para '{k}'")
                        print(f"==>{rst}")
                except Exception as e:
                    print("=================ERRO===========")
                    print(f"Erro ao processar a resposta da API para a palavra-chave '{k}': {e}")
                    print(f"URL: {url}")
        return True
    else:
        return False