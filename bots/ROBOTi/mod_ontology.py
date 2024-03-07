import database

def checkDataConceptExist():
    qr = "select * "
    qr = "select * FROM brapci_rdf.rdf_data "
    qr += "left join brapci_rdf.rdf_concept ON d_r2 = id_cc "
    qr += "where d_trust = 0 and id_cc is null limit 10 "
    print("103 - Checando Existencia das relacoes R2")
    row = database.query(qr)
    print(row)

def checkData():
    qr = "select * "
    qr = "select * FROM brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_concept ON d_r2 = id_cc "
    qr += "left join brapci_rdf.rdf_class_domain ON d_p = id_cd "
    qr += "where d_trust = 0 limit 30000 "
    print("100 - Checando Ontologias")

    row = database.query(qr)

    qu = ""
    n = 0
    for item in row:
        ID = item[0]
        C2 = item[1]
        C3 = item[2]

        CA1 = item[3]
        CA2 = item[4]

        if C2 == C3:
            qu = f"update brapci_rdf.rdf_data set d_trust = 1 where id_d = {ID}"
            print(".", end='')
            n = n + 1
            if (n > 50):
                n = 0
                print("")
            database.update(qu)
        else:
            print(ID,C2,C3,CA1,CA2)
            qu = f"update brapci_rdf.rdf_data set d_trust = -1 where id_d = {ID}"
            database.update(qu)

    #qr = "update brapci_rdf.rdf_data set d_library = d_r1, d_r1 = d_r2, d_r2 = d_library, d_trust = 0, d_library = 0 where d_trust = -1"
