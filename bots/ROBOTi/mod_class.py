import database

def getClass(cl):

    qr = "select * from brapci_rdf.rdf_class "
    qr += f" where (c_class  = '{cl}')"

    row = database.query(qr)
    if row != []:
        ID = row[0][0]
    else:
        print(f"Class {cl} not found")
        quit()
    return ID
