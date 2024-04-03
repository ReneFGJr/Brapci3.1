import fitz  # Importa PyMuPDF
import re
import database
import mod_data
import mod_class
import mod_literal
import mod_GoogleTranslate

def convert(ID):
    prop = mod_class.getClass("hasFileStorage")
    qr = "select n_name, n_lang from brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_concept ON d_r2 = id_cc "
    qr += "inner join brapci_rdf.rdf_literal ON cc_pref_term = id_n "
    qr += f" where d_r1 = {ID} and d_p = {prop}"
    row = database.query(qr)

    for ln in row:
        print(ln[0])

# Função para extrair texto do PDF
def extrair_texto_pdf(caminho_arquivo):
    texto = ""
    with fitz.open(caminho_arquivo) as doc:
        for pagina in doc:
            texto += pagina.get_text()

    caminho_arquivo_txt = caminho_arquivo.replace('.pdf','.txt')

    with open(caminho_arquivo_txt, 'w', encoding='utf-8') as arquivo:
        arquivo.write(texto)

    return texto
