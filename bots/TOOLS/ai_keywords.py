import re
import sys
import mod_api

def locateKeywords(text):
    t = {'Palavras–chave:','Palavras-Chave:'}
    for te in t:
        if te in text:
            return te
    return ""

def locateAbstract(text):
    t = {'Abstract:','ABSTRACT:','Abstract','ABSTRACT'}
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
        sys.exit()
    else:
        print("======>",keyw,term)
    exp = r"{keyw}\s*(.*?)(?={term})"
    print("EXP",exp)
    match = re.search(exp, text, re.DOTALL)

    print(match)
    sys.exit()

    if match:
        keywords = match.group(1).split(";")
        keys = [keyword.strip().capitalize() for keyword in keywords if keyword.strip()]
        print("KEYS",keys)
        urlKey = 'https://cip.brapci.inf.br/api/rdf/createConcept/Subject?lang=pt&name='
        for k in keys:
            if k != '':
                data = {'apikey': k}
                rst = mod_api.api_post(urlKey + k, data)

                print(k)
                sys.exit()

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