import re
import sys
import mod_api
from colorama import Fore

def locateKeywords(text):
    t = {
        'Palavras–chave:', 'Palavras-Chave:', 'Palavras-chave:',
        'Palavra-chave:', 'Palavra-Chave:',
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

def extract_keywords(text,id):
    text = text.replace(chr(10), ' ')
    text = text.replace('.', ';')
    text = text.replace(',', ';')
    text = text.replace('Palavras-Chave','Palavras-chave')
    text = text.replace('Palavras Chave','Palavras-chave')
    text = text.replace('PALAVRAS-CHAVE:','Palavras-chave:')
    term = locateAbstract(text)
    keyw = locateKeywords(text)

    if term == '' or keyw == '':
        print(f"Área não localizada [{keyw}],[{term}]")
        print(text[:2000])
        sys.exit()
    print("=TERM & KEY Position=====>",keyw,term)

    exp = f"{keyw}\s*(.*?)(?={term})"
    #exp = f"{keyw}\\s*(.*?)(?={term})"

    match = re.search(exp, text, re.DOTALL)
    stop = 0

    if match:
        keywords = match.group(1).split(";")
        keys = [keyword.strip().capitalize() for keyword in keywords if keyword.strip()]
        urlKey = 'https://cip.brapci.inf.br/api/rdf/createConcept/Subject?lang=pt&name='
        tkey = 0

        size = 55

        for k in keys:
            if (len(k) >= size):
                print("Termo muito longo: ",Fore.RED,len(k),k,Fore.WHITE)
                stop = 1
            if (len(k) <= 2):
                stop = 1

            print("==>Processando:",Fore.BLUE,k,Fore.WHITE)

            if k != '' and len(k) < size and stop == 0 and tkey <= 6:
                tkey = tkey + 1
                data = {'apikey': k}
                rst = mod_api.api_post(urlKey + k, data)

                try:
                    # Verificar se 'rst' é um dicionário e possui a chave 'id'
                    if isinstance(rst, dict) and 'id' in rst:
                        idr = rst['id']
                        url = f'https://cip.brapci.inf.br/api/rdf/dataAdd/?source={id}&prop=hasSubject&resource={idr}'
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
        return True
    else:
        return False
