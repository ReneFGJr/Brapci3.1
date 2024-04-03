import database
import mod_class
import mod_literal
import mod_GoogleTranslate

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
        if (lg == 'pt'):
            pt = True
        elif (lg == 'en'):
            en = True
        elif (lg == 'es'):
            es = True

    if (en):
        term = "Ola Mundo"
        mod_GoogleTranslate
        mod_literal.register(term,'en')