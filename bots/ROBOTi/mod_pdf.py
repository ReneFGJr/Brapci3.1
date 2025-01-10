import fitz  # Importa PyMuPDF
import re
import os
import database
import mod_data
import mod_class
import mod_concept
import mod_literal
import mod_GoogleTranslate

def harvestingPDF():
    qr = "select ID from brapci_elastic.dataset "
    qr += " where "
    qr += "(CLASS = 'Article' or CLASS='Proceeding')"
    qr += " order by ID DESC "
    qr += " limit 1"
    row = database.query(qr)

    for line in row:
        ID = line[0]
        getPDF(ID)
    print("Fim da coleta de PDF")
    return ""

def getPDF(ID):
    row = mod_concept.le(ID)
    prop = mod_class.getClass("hasFileStorage")
    qr = "select n_name, n_lang from brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_concept ON d_r2 = id_cc "
    qr += "inner join brapci_rdf.rdf_class ON cc_class = id_c "
    qr += "inner join brapci_rdf.rdf_literal ON cc_pref_term = id_n "
    qr += f" where d_r1 = {ID} and d_p = {prop}"
    row = database.query(qr)


def convert(ID):
    prop = mod_class.getClass("hasFileStorage")
    qr = "select n_name, n_lang from brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_concept ON d_r2 = id_cc "
    qr += "inner join brapci_rdf.rdf_literal ON cc_pref_term = id_n "
    qr += f" where d_r1 = {ID} and d_p = {prop}"
    row = database.query(qr)

    for ln in row:
        file = '/data/Brapci3.1/public/' + ln[0]
        print(file)
        if os.path.isfile(file):
            extrair_texto_pdf(file)
            print("  OK")
        else:
            print("  FILE NOT FOUND")


# Função para extrair texto do PDF
def extrair_texto_pdf(caminho_arquivo):
    texto = ""
    with fitz.open(caminho_arquivo) as doc:
        for pagina in doc:
            texto += pagina.get_text()

    caminho_arquivo_txt = caminho_arquivo.replace('.pdf','.txt')

    with open(caminho_arquivo_txt, 'w', encoding='utf-8') as arquivo:
        arquivo.write(texto)
        print("Convertido ",caminho_arquivo_txt)

    return texto
