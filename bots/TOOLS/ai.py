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
import ai_abstract

import mod_convert_repository
import database

import mod_small_world
import mod_abstract

def toDO():
    qr = "select ID from brapci_elastic.dataset where JOURNAL = 75 and (ABSTRACT = '' OR KEYWORDS = '')"
    row = database.query(qr)

    nome_arquivo = "toDO.sh"
    with open(nome_arquivo, "w", encoding="utf-8") as arquivo:
        for r in row:
            id = r[0]
            arquivo.write(f"python3 ai.py All {id}\n")
    print("Arquivo gerado:",nome_arquivo)

def version():
    return "v0.26.06.13"

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
print (" - Checando diretorio:",dir)
os.chdir(dir)

print (" - Checando argumentos")

if (len(sys.argv) > 1):
    parm = sys.argv
    act = parm[1]
    if (act == 'X'):
        print("Extrair Trabalhos")
        lists = toDO()
        sys.exit()
    if (len(sys.argv) > 2):
        id = parm[2]
    else:
        id = 0
else:
    id = 309177
    act = 'email'



dirT = '/data/Brapci3.1/public/'
if (id != 0):
    idO = id
    file = dirT + sys_io.getNameFile(id)
    print("Processando ID #1",id)

    # Valida se o arquivo foi encontrado
    if not file or file == dirT or not os.path.isfile(file):
        print(f"Erro no ID {id}")
        print(f"Arquivo não encontrado: {file}")
        sys.exit(1)

    fileTXT = sys_io.getNameFileTXT(file)

    # Valida se o arquivo TXT é válido
    if not fileTXT or not os.path.isfile(fileTXT):
        print(f"Erro no ID {id}")
        print(f"Arquivo TXT não encontrado ou inválido: {fileTXT}")
        sys.exit(1)

    txt = sys_io.readfile(fileTXT)

    # Valida se o conteúdo foi lido
    if not txt:
        print(f"Erro no ID {id}")
        print(f"Não foi possível ler o arquivo: {fileTXT}")
        sys.exit(1)

    fileO = file
else:
    fileO = 'x'
sys.exit()
if ((act == 'All') or (act == 'all')):
    print("Extrair Trabalhos v2")
    print("=fileO=>",fileO)
    # Checar existencia do aquivo Markdown


    print("<h4>Gerar Markdown</h4>")
    print(" Processoando ID",id)
    import mod_docling
    mod_docling.save_file_docling(fileO, id)

    print("Extrair e-mail")
    lists = ai_email.extrair_emails(txt)
    fileN = fileO.replace('.pdf','_email.json')
    saveFileD(fileN,lists)
    print("Extrair URL")
    lists = ai_url.extrair_urls(txt)
    fileN = fileO.replace('.pdf','_url.json')
    saveFileD(fileN,lists)
    print("Extrair DOI")
    ai_doi_handle.doi(txt, id)
    lists = ai_doi_handle.extrair_doi(txt)
    fileN = fileO.replace('.pdf','_doi.json')
    saveFileD(fileN,lists)
    print("Extrair HANDLE")
    lists = ai_doi_handle.extrair_handle(txt)
    fileN = fileO.replace('.pdf','_handle.json')
    saveFileD(fileN,lists)
    print("Extrair Metadados")
    lists = ai_metadados.extrair_secoes_method_01(txt)
    fileN = fileO.replace('.pdf','_metadados.json')
    saveFileD(fileN,lists)

    print("Extrair Citações")
    lists = ai_cited.extrair_referencias_v2(id)

    print("Extrair Sessões")
    lists = ai_section.extrair_sessao(txt,id)
    print("Extrair Keywords")
    lists = ai_keywords.extract_keywords(txt,id)
    print("==>",fileO)
    fileN = fileO.replace('.pdf','_keywords.json')
    saveFileD(fileN,lists)
    lists = ai_abstract.extract_abstract(txt, id)
    sys.exit()

elif (act == 'docling'):
    print("Extrair Markdown - Docling")
    print("  ",fileO,id)
    import mod_docling
    mod_docling.save_file_docling(fileO, id)

elif (act == 'email'):
    print("Extrair e-mail")
    lists = ai_email.extrair_emails(txt)
    fileN = fileO.replace('.pdf','_email.json')
    saveFileD(fileN,lists)

elif (act == 'url'):
    print("Extrair URL")
    lists = ai_url.extrair_urls(txt)
    fileN = fileO.replace('.pdf','_url.json')
    saveFileD(fileN,lists)

elif (act == 'doi'):
    print("Extrair DOI")
    ai_doi_handle.doi(txt, id)
    lists = ai_doi_handle.extrair_doi(txt)
    fileN = fileO.replace('.pdf','_doi.json')
    saveFileD(fileN,lists)

elif (act == 'handle'):
    print("Extrair HANDLE")
    lists = ai_doi_handle.extrair_handle(txt)
    fileN = fileO.replace('.pdf','_handle.json')
    saveFileD(fileN,lists)

elif (act == 'metadata'):
    print("Extrair Metadados")
    lists = ai_metadados.extrair_secoes_method_01(txt)
    fileN = fileO.replace('.pdf','_metadados.json')
    saveFileD(fileN,lists)

elif (act == 'cited'):
    print("Extrair Citações")
    lists = ai_cited.extrair_referencias_v2(id)

elif (act == 'section'):
    print("Extrair Sessões")
    lists = ai_section.extrair_sessao(txt,id)

elif (act == 'keywords'):
    print("Extrair Keywords")
    lists = ai_keywords.extract_keywords(txt,id)
    print("==>",fileO)
    fileN = fileO.replace('.pdf', '_keywords.json')
    saveFileD(fileN, lists)


elif (act == 'keywords_ia'):
    print("=Usando OA model=")
    mod_abstract.main(idO,keywords=True)


elif (act == 'keywordsOllama'):
    print("Extrair Keywords Ollama")
    lists = ai_keywords.extract_keywords_ollama(txt, id)
    print("==>", fileO)
    #fileN = fileO.replace('.pdf', '_keywords.json')
    #saveFileD(fileN, lists)

elif (act == 'abstract'):
    lists = ai_abstract.extract_abstract(txt,id)
    if (lists==""):
        print("=Usando OA model=")
        mod_abstract.main(idO)

elif (act == 'abstract_ia'):
    print("=Usando OA model=")
    mod_abstract.main(idO)

########## Small World
elif (act == 'smallWorld'):
    mod_small_world.proccess()
elif (act == 'sw'):
    mod_small_world.proccess()
else:
    print("Ação não localizada")
    print("email, url, doi, handle, metadata, cited, section, keywords, docling")
    sys.exit()
