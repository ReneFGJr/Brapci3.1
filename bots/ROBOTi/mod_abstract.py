import database
import mod_class
import sys

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import numpy as np


def check_double():
    prop = 'hasAbstract'
    IDprop = mod_class.getClass(prop)

    qr = "select * from ( "
    qr += "SELECT count(*) as total, d_r1, n_lang "
    qr += "FROM brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_literal ON d_literal = id_n "
    qr += f"where d_p = {IDprop} "
    qr += "group by d_r1, n_lang "
    qr += ") as tabela "
    qr += "WHERE total > 1;"

    row = database.query(qr)
    for line in row:
        print(line)
        print("=======================")
        ID = line[1]
        lang = line[2]
        removeDouble(ID,lang,IDprop)

def removeDouble(ID,lang,IDprop):
    qr = "select id_d, n_name, n_lang "
    qr += " FROM brapci_rdf.rdf_data "
    qr += " inner join brapci_rdf.rdf_literal ON d_literal = id_n "
    qr += f" where d_r1 = {ID} and d_p = {IDprop} and n_lang = '{lang}' "
    qr += " order by id_n "
    row = database.query(qr)

    ln1 = row[0]
    ln2 = row[1]
    print(grau_de_equivalencia(ln1,ln2))
    sys.exit()

def grau_de_equivalencia(texto1, texto2):
    # Vetorização dos textos usando TF-IDF (Term Frequency - Inverse Document Frequency)
    vectorizer = TfidfVectorizer().fit_transform([texto1, texto2])

    # Calcula a similaridade de cosseno entre os dois textos
    cosine_sim = cosine_similarity(vectorizer[0:1], vectorizer[1:2]).flatten()[0]

    # Converte para uma escala de 0 a 100
    grau_equivalencia = round(cosine_sim * 100, 2)

    return grau_equivalencia