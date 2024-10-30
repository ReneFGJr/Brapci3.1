import re
import sys
import mod_api

def extract_keywords(text,id):
    text = text.replace(chr(10), ' ')
    text = text.replace('.', ';')
    text = text.replace('Palavras-Chave','Palavras-chave')
    text = text.replace('Palavras Chave','Palavras-chave')
    match = re.search(r"Palavras-chave:\s*(.*?)(?=Abstract:)", text, re.DOTALL)

    if match:
        keywords = match.group(1).split(";")
        keys = [keyword.strip().capitalize() for keyword in keywords if keyword.strip()]

        urlKey = 'https://cip.brapci.inf.br/api/rdf/createConcept/Subject?lang=pt&name='
        for k in keys:
            if k != '':
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