import database

def cited():
    qr = "select * from brapci_rdf.rdf_literal "
    qr += "where n_name like '%/*ref*/%'"
    row = database.query(qr)

    for item in row:
        print(item)