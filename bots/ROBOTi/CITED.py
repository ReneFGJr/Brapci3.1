import os
import mod_http
import mod_xml_soup
import mod_class
import database

diretorio = '/data/Brapci3.1/bots/ROBOTi'
os.chdir(diretorio)

import mod_cited
import sys

def getURL(ID):
    prop = mod_class.getClass("hasUrl")
    qr = "select n_name, n_lang from brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_literal ON d_literal = id_n"
    qr += f" where d_r1 = {ID} and d_p = {prop}"
    print(qr)
    row = database.query(qr)

    print(row)

def getCITED(url,ID):
    url = 'https://awari.pro-metrics.org/index.php/a/article/view/47'
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

    getURL(ID)