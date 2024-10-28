import re
import json
import sys
import sys_io

import ai_email
import ai_url
import ai_doi_handle
import ai_metadados

def version():
    return "v0.24.10.27"

def extrair_emails(texto):
    # Expressão regular para detectar e-mails
    padrao_email = r'[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}'

    # Busca todos os e-mails no texto
    emails = re.findall(padrao_email, texto)

    return emails


########################################### Início
print("TOOLS e-mail",version())
print("===============================================")

if (len(sys.argv) > 1):
    parm = sys.argv
    id = parm[2]
    act = parm[1]
else:
    id = 309177
    act = 'email'

file = sys_io.getNameFile(id)
txt = sys_io.readfile(file)

run = 0

if (act == 'email'):
    run = 1
    print("Extrair e-mail")
    lists = ai_email.extrair_emails(txt)
    fileN = file.replace('.txt','_email.json')

if (act == 'url'):
    run = 1
    print("Extrair URL")
    lists = ai_url.extrair_urls(txt)
    fileN = file.replace('.txt','_url.json')

if (act == 'doi'):
    run = 1
    print("Extrair DOI")
    lists = ai_doi_handle.extrair_doi(txt)
    fileN = file.replace('.txt','_doi.json')

if (act == 'handle'):
    run = 1
    print("Extrair HANDLE")
    lists = ai_doi_handle.extrair_handle(txt)
    fileN = file.replace('.txt','_handle.json')

if (act == 'metadata'):
    run = 1
    print("Extrair Metadados")
    lists = ai_metadados.extrair_secoes_method_01(txt)
    fileN = file.replace('.txt','__metadados.json')

    print(lists)

if (run == 0):
    print(parm)
else:
    # Salva a lista em um arquivo JSON
    with open(fileN, "w", encoding="utf-8") as arquivo:
        json.dump(lists, arquivo, ensure_ascii=False, indent=4)