import database
import mod_class

def createTerm(term,lang,th):

    return ""

def check_subject_thesa():
    print("Check Subject - Thesa")
    IDClass = mod_class.getClass("Subject")

    qr = "select id_cc, cc_use, n_name, n_lang  "
    qr += " from brapci_rdf.rdf_concept "
    qr += " inner join brapci_rdf.rdf_literal ON id_n = cc_pref_term"
    qr += f" where cc_class = {IDClass}"
    qr += " and id_cc = cc_use "
    qr += " order by n_name, id_cc"
    qr += " limit 10 "

    row = database.query(qr)
    print(row)
