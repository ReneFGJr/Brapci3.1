import re
import os
import json
import sys
import sys_io

import ai_email
import ai_url
import ai_doi_handle
import ai_metadados
import ai_cited
import ai_keywords
import ai_section

def version():
    return "v0.24.10.27"

def extrair_emails(texto):
    # Expressão regular para detectar e-mails
    padrao_email = r'[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}'

    # Busca todos os e-mails no texto
    emails = re.findall(padrao_email, texto)

    return emails

def saveFileD(fileN,arquivo):
    # Salva a lista em um arquivo JSON
    with open(fileN, "w", encoding="utf-8") as arquivo:
        json.dump(lists, arquivo, ensure_ascii=False, indent=4)

    return True

########################################### Início
print("TOOLS AI",version())
print("===============================================")
dir = '/data/Brapci3.1/bots/TOOLS'
os.chdir(dir)

if (len(sys.argv) > 1):
    parm = sys.argv
    id = parm[2]
    act = parm[1]
else:
    id = 309177
    act = 'email'

dirT = '../../public/'
fileO = sys_io.getNameFileX(id)

file = sys_io.getNameFile(id)
fileTXT = sys_io.getNameFileTXT(file)
txt = sys_io.readfile(fileTXT)

if (act == 'email'):
    print("Extrair e-mail")
    lists = ai_email.extrair_emails(txt)
    fileN = fileO.replace('.txt','_email.json')
    saveFileD(fileN,lists)

if (act == 'url'):
    print("Extrair URL")
    lists = ai_url.extrair_urls(txt)
    fileN = fileO.replace('.txt','_url.json')
    saveFileD(fileN,lists)

if (act == 'doi'):
    print("Extrair DOI")
    lists = ai_doi_handle.extrair_doi(txt)
    fileN = fileO.replace('.txt','_doi.json')
    saveFileD(fileN,lists)

if (act == 'handle'):
    print("Extrair HANDLE")
    lists = ai_doi_handle.extrair_handle(txt)
    fileN = fileO.replace('.txt','_handle.json')
    saveFileD(fileN,lists)

if (act == 'metadata'):
    print("Extrair Metadados")
    lists = ai_metadados.extrair_secoes_method_01(txt)
    fileN = fileO.replace('.txt','_metadados.json')
    saveFileD(fileN,lists)

if (act == 'cited'):
    print("Extrair Citações")
    lists = ai_cited.extrair_referencias(txt,id)
    fileN = fileO.replace('.txt','_cited.json')
    saveFileD(fileN,lists)

if (act == 'section'):
    print("Extrair Sessões")
    lists = ai_section.extrair_sessao(txt,id)

if (act == 'keywords'):
    print("Extrair Keywords")
    lists = ai_keywords.extract_keywords(txt,id)
    fileN = fileO.replace('.txt','_keywords.json')
    saveFileD(fileN,lists)
