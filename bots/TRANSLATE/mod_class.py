import database
import mod_class

def getClass(Class):
    qr = "select * from brapci_rdf.rdf_class "
    qr += f" where (c_class  = '{Class}')"

    row = database.query(qr)
    if row != []:
        ID = row[0][0]
    else:
        print(f"Class {Class} not found")
        quit()
    return ID
