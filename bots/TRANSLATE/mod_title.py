import database
import mod_class

def title(ID):
    prop = mod_class.getClass("hasTitle")
    qr = f"select * from brapci_rdf.rdf_data where d_r1 = {ID} and d_p = {prop}"
    print(qr)
