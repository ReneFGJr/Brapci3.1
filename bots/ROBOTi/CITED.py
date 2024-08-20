import os
import mod_http
import mod_xml_soup
import mod_class
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
        getCITED(URL,ID)

def getCITED(url,ID):
    html = mod_http.getURL(url)

    cited = mod_xml_soup.readMeta(html,'citation_reference')

    # Lista os metadados encontrados
    print("Citation References:")
    for meta in cited:
        print("=",meta.get('content'))

print("RASPAGEM DE CITACOES 1.1")

if (len(sys.argv) > 1):
    parm = sys.argv
    ID = parm[1]

    processID(ID)