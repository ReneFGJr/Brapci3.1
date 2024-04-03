import database
import mod_class

def title(ID):
    prop = mod_class.getClass("hasTitle")
    qr = "select n_name, n_lang from brapci_rdf.rdf_data "
    qr = "innher join brapci_rdf.rdf_data d_literal = id_n"
    qr += f" where d_r1 = {ID} and d_p = {prop}"
    row = database.query(qr)
    print(row)
