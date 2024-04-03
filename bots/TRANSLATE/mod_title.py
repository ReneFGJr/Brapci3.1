import database

def title(ID):
    prop =
    qr = "select * from brapci_rdf.rdf_data where d_r1 = {ID} and d_p = {prop}"