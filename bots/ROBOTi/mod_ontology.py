import database

def checkData():
    qr = "select * "
    qr = "select id_d, C2.cc_class as class2, cr_range, d_r1, d_r2 "
    qr += "FROM brapci_rdf.rdf_data "
    qr += "inner Join brapci_rdf.rdf_class_domain ON cr_property = d_p "
    qr += "Inner Join brapci_rdf.rdf_concept as C2 ON C2.id_cc = d_r2 "
    qr += "where d_trust = 0 "
    qr += "limit 1000"
    print(qr)

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

    qr = "update brapci_rdf.rdf_data set d_library = d_r1, d_r1 = d_r2, d_r2 = d_library, d_trust = 0, d_library = 0 where d_trust = -1"
