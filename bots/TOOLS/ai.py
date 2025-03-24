import re
import os
import json
import sys
import sys_io
import database

import ai_email
import ai_url
import ai_doi_handle
import ai_metadados
import ai_cited
import ai_keywords
import ai_section
import ai_abstract
import mod_convert_repository
import mod_docling


def Xecute(id, act):

    dirT = '/data/Brapci3.1/public/'

    file = dirT + sys_io.getNameFile(id)
    fileTXT = sys_io.getNameFileTXT(file)
    txt = sys_io.readfile(fileTXT)
    fileO = file
    print("=fileO=>", file)

    if (act == 'All'):
        print("<h4>Gerar Markdown</h4>")
        print(" Processoando ID", id)
        mod_docling.saveFileD(fileO)

        print("Extrair e-mail")
        lists = ai_email.extrair_emails(txt)
        fileN = fileO.replace('.pdf', '_email.json')
        saveFileD(fileN, lists)
        print("Extrair URL")
        lists = ai_url.extrair_urls(txt)
        fileN = fileO.replace('.pdf', '_url.json')
        saveFileD(fileN, lists)
        print("Extrair DOI")
        lists = ai_doi_handle.extrair_doi(txt)
        fileN = fileO.replace('.pdf', '_doi.json')
        saveFileD(fileN, lists)
        print("Extrair HANDLE")
        lists = ai_doi_handle.extrair_handle(txt)
        fileN = fileO.replace('.pdf', '_handle.json')
        saveFileD(fileN, lists)
        print("Extrair Metadados")
        lists = ai_metadados.extrair_secoes_method_01(txt)
        fileN = fileO.replace('.pdf', '_metadados.json')
        saveFileD(fileN, lists)
        print("Extrair Citações")
        lists = ai_cited.extrair_referencias(txt, id)
        fileN = fileO.replace('.pdf', '_cited.json')
        saveFileD(fileN, lists)
        print("Extrair Sessões")
        lists = ai_section.extrair_sessao(txt, id)
        print("Extrair Keywords")
        lists = ai_keywords.extract_keywords(txt, id)
        print("==>", fileO)
        fileN = fileO.replace('.pdf', '_keywords.json')
        saveFileD(fileN, lists)
        lists = ai_abstract.extract_abstract(txt, id)
        sys.exit()

    elif (act == 'docling'):
        print("Extrair Markdown")
        lists = mod_docling.saveFileD(fileO)

    elif (act == 'email'):
        print("Extrair e-mail")
        lists = ai_email.extrair_emails(txt)
        fileN = fileO.replace('.pdf', '_email.json')
        saveFileD(fileN, lists)

    elif (act == 'url'):
        print("Extrair URL")
        lists = ai_url.extrair_urls(txt)
        fileN = fileO.replace('.pdf', '_url.json')
        saveFileD(fileN, lists)

    elif (act == 'doi'):
        print("Extrair DOI")
        lists = ai_doi_handle.extrair_doi(txt)
        fileN = fileO.replace('.pdf', '_doi.json')
        saveFileD(fileN, lists)

    elif (act == 'handle'):
        print("Extrair HANDLE")
        lists = ai_doi_handle.extrair_handle(txt)
        fileN = fileO.replace('.pdf', '_handle.json')
        saveFileD(fileN, lists)

    elif (act == 'metadata'):
        print("Extrair Metadados")
        lists = ai_metadados.extrair_secoes_method_01(txt)
        fileN = fileO.replace('.pdf', '_metadados.json')
        saveFileD(fileN, lists)

    elif (act == 'cited'):
        print("Extrair Citações")
        lists = ai_cited.extrair_referencias(txt, id)
        fileN = fileO.replace('.pdf', '_cited.json')
        saveFileD(fileN, lists)

    elif (act == 'section'):
        print("Extrair Sessões")
        lists = ai_section.extrair_sessao(txt, id)

    elif (act == 'keywords'):
        print("Extrair Keywords")
        lists = ai_keywords.extract_keywords(txt, id)
        print("==>", fileO)
        fileN = fileO.replace('.pdf', '_keywords.json')
        saveFileD(fileN, lists)
    elif (act == 'abstract'):
        lists = ai_abstract.extract_abstract(txt, id)
    else:
        print("Ação não localizada")
        print(
            "email, url, doi, handle, metadata, cited, section, keywords, docling"
        )
        sys.exit()

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
    if (len(parm) > 2):
        id = parm[2]
    else:
        id = 0
    act = parm[1]
else:
    id = 309177
    act = 'email'

if (act == 'X'):
    print("Processando todos os itens")
    qr = "select ID from brapci_elastic.dataset where JOURNAL = 75 and ABSTRACT = '' "
    row = database.query(qr)
    print(row)
else:
    Xecute(id, act)