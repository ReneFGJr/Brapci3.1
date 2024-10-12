import database
import mod_class
import sys
import html

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import numpy as np

def charSet():
    qr = "SELECT id_n, n_name FROM brapci_rdf.rdf_literal WHERE n_name LIKE '%&%;'"
    row = database.query(qr)

    for record in row:
        record_id = record[0]
        l_name_html = record[1]

        # Converte as entidades HTML para texto normal
        l_name_converted = convert_html_entities(l_name_html)

        # Atualiza o campo l_name com o valor convertido
        qu = "UPDATE brapci_rdf.rdf_name SET n_name = '{l_name_converted}' WHERE id_n = {id_n}"
        print(l_name_html)
        print(qu)
        sys.exit()

def check_double():
    charSet()
    sys.exit()
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
        ID = line[1]
        lang = line[2]
        removeDouble(ID,lang,IDprop)

def solicitar_confirmacao():
    resposta = input("Tem certeza que deseja excluir? (s/n/2): ").lower()
    if resposta == 's':
        return 1
    if resposta == '2':
        return 2
    elif resposta == 'n':
        return 0
    else:
        print("Resposta inválida. Por favor, digite 's' para sim ou 'n' para não.")
        solicitar_confirmacao()  # Chama novamente a função para solicitar confirmação

def removeDouble(ID,lang,IDprop):
    qr = "select id_d, n_name, n_lang "
    qr += " FROM brapci_rdf.rdf_data "
    qr += " inner join brapci_rdf.rdf_literal ON d_literal = id_n "
    qr += f" where d_r1 = {ID} and d_p = {IDprop} and n_lang = '{lang}' "
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
        print("#1#",ln1)
        print("#2#",ln2)
        rsp = solicitar_confirmacao()
        if (rsp > 0):
            if (rsp == 1):
                qd = f"delete from brapci_rdf.rdf_data where id_d = {ID1}"
            else:
                qd = f"delete from brapci_rdf.rdf_data where id_d = {ID2}"
            database.query(qd)
        else:
            sys.exit()

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