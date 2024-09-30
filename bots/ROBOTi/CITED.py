import os
import mod_http
import mod_xml_soup
import mod_class
import mod_cited
import database

diretorio = '/data/Brapci3.1/bots/ROBOTi'
os.chdir(diretorio)

import mod_cited
import sys

def processID(ID):
    prop = mod_class.getClass("hasUrl")
    qr = "select n_name, n_lang from brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_literal ON d_literal = id_n"
    qr += f" where d_r1 = {ID} and d_p = {prop}"
    row = database.query(qr)

    for ln in row:
        URL = ln[0]
        print("Recuperando ",URL)
        getCITED(URL,ID)

def getCITED(url,ID):
    try:
        html = mod_http.getURL(url)

        cited = mod_xml_soup.readMeta(html,'citation_reference')

        # Lista os metadados encontrados
        print("Citation References:")
        cites = False
        for meta in cited:
            cites = True
            print("=",meta.get('content'))

        if cites == True:
            print("Deleta citações autuais")
            mod_cited.delete(ID)

            for meta in cited:
                REF = meta.get('content')
                REF = REF.replace("'","´")
                mod_cited.register(ID,REF)
    except Exception as e:
        print(f"Ocorreu um erro: {e}")


print("RASPAGEM DE CITACOES 1.1")

def updateCited(ID):
    qr = f"SELECT count(*) as total FROM `cited_article` WHERE `ca_rdf` = {ID};"
    row = database.query(qr)
    total = row[0][0]
    print("Total ",total)

    qu = f"update brapci_elastic.dataset set cited_total = {total} WHERE `ID` = {ID};"
    row = database.update(qu)

def autoHarvesting():
    qr = "select ID from brapci_elastic.dataset where cited_total = -1 and CLASS = 'Article' order by id_ds desc limit 10"
    row = database.query(qr)

    for line in row:
        print(line)
        print("======================")
        processID(ID)
        updateCited(ID)

if (len(sys.argv) > 1):
    parm = sys.argv
    ID = parm[1]

    if (ID == 'auto'):
        autoHarvesting()
    else:
        processID(ID)