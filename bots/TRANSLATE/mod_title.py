import database
import mod_class

def title(ID):
    prop = mod_class.getClass("hasTitle")
    qr = "select n_name, n_lang from brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_literal ON d_literal = id_n"
    qr += f" where d_r1 = {ID} and d_p = {prop}"
    row = database.query(qr)

    pt = False
    en = False
    es = False

    for item in row:
        lg = item[1]
        txt = item[0]
        print(lg,item)