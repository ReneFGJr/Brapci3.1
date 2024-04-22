import database

def cited():
    qr = "select id_n, d_r1, n_name from brapci_rdf.rdf_literal "
    qr += "inner join brapci_rdf.rdf_data ON id_n = d_literal "
    qr += "where n_name like '%/*ref*/%'"
    row = database.query(qr)

    for item in row:
        print(item)
        ID = item[1]
        IDn = item[0]
        REF = item[2]

        REF = REF.replace('/*ref*/','').strip()
        print(ID,IDn,REF)
        quit()