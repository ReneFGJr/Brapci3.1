import database
import mod_class
import sys
import html

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import numpy as np

def charSet():
    qr = "SELECT id_n, n_name FROM brapci_rdf.rdf_literal WHERE "
    qr += "n_name LIKE '%&atilde;%' "
    qr += "OR n_name LIKE '%&eacute;%' "
    qr += "OR n_name LIKE '%&iacute;%' "
    qr += "OR n_name LIKE '%&ocirc;%' "
    qr += "OR n_name LIKE '%&uacute;%' "
    qr += "OR n_name LIKE '%&ccedil;%' "
    qr += "OR n_name LIKE '%&ntilde;%' "
    qr += "OR n_name LIKE '%&quot;%' "
    qr += "OR n_name LIKE '%&amp;%' "
    qr += "OR n_name LIKE '%&nbsp;%' "

    row = database.query(qr)

    for record in row:
        record_id = record[0]
        n_name_html = record[1]

        # Converte as entidades HTML para texto normal
        n_name_converted = html.unescape(n_name_html).strip()

        # Atualiza o campo n_name com o valor convertido
        qu = f"UPDATE brapci_rdf.rdf_literal SET n_name = '{n_name_converted}' WHERE id_n = {record_id}"
        print("     charSet Update",record_id)
        database.update(qu)


def check_double():
    charSet()

    prop = 'hasAbstract'
    prop = 'hasTitle'
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
        ID = line[1]
        lang = line[2]
        removeDouble(ID,lang,IDprop)

def solicitar_confirmacao(ID):
    resposta = input(f"Tem certeza que deseja excluir {ID} (s/n/1/2): ").lower()
    if resposta == 's':
        return 1
    if resposta == '1':
        return 1
    if resposta == '2':
        return 2
    elif resposta == 'n':
        return -1
    else:
        return -1

def removeDouble(ID,lang,IDprop):
    qr = "select id_d, n_name, n_lang, d_r1 "
    qr += " FROM brapci_rdf.rdf_data "
    qr += " inner join brapci_rdf.rdf_literal ON d_literal = id_n "
    qr += f" where (d_r1 = {ID}) and (d_p = {IDprop}) and (n_lang = '{lang}') "
    qr += " order by id_n "
    row = database.query(qr)

    ln1 = row[0][1]
    ln2 = row[1][1]
    ID1 = row[0][0]
    ID2 = row[1][0]

    gr = grau_de_equivalencia(ln1,ln2)
    print("===",ID,gr)
    if (gr > 90):
        qd = f"delete from brapci_rdf.rdf_data where id_d = {ID1}"
        database.query(qd)
    else:
        print("#1#",row[0][2],ln1)
        print(" ")
        print("#2#",row[0][2],ln2)
        print(" ")
        rsp = solicitar_confirmacao(ID)
        if (rsp > 0):
            if (rsp == 1):
                qd = f"delete from brapci_rdf.rdf_data where id_d = {ID1}"
            else:
                qd = f"delete from brapci_rdf.rdf_data where id_d = {ID2}"
            database.query(qd)
        else:
            print("#########SKIP")

def grau_de_equivalencia(texto1, texto2):

    # Garantindo que os textos sejam strings
    if not isinstance(texto1, str):
        texto1 = str(texto1) if texto1 is not None else ""
    if not isinstance(texto2, str):
        texto2 = str(texto2) if texto2 is not None else ""

    # Vetorização dos textos usando TF-IDF (Term Frequency - Inverse Document Frequency)
    vectorizer = TfidfVectorizer().fit_transform([texto1, texto2])

    # Calcula a similaridade de cosseno entre os dois textos
    cosine_sim = cosine_similarity(vectorizer[0:1], vectorizer[1:2]).flatten()[0]

    # Converte para uma escala de 0 a 100
    grau_equivalencia = round(cosine_sim * 100, 2)

    return grau_equivalencia