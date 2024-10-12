import database
import mod_class
import sys


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
    for line in row:
        print(line)
    sys.exit()