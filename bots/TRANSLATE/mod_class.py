import database
import mod_class

def getClass(cl):
    Class = mod_class.getClass('hasTitle')

    qr = "select * from brapci_rdf.rdf_class "
    qr += f" where (c_class  = '{Class}')"

    print(qr)

    row = database.query(qr)
    if row != []:
        ID = row[0][0]
    else:
        print(f"Class {cl} not found")
        quit()
    return ID
