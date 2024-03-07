import database

def resume():
    qr = "SELECT count(*) as total, d_trust "
    qr += " FROM brapci_rdf.rdf_data "
    qr += " group by d_trust"
    row = database.query(qr)
    print(row)

def checkDataInverse():
    qr = "SELECT data1.d_r1, data1.d_r2, data2.d_r1, data2.d_r2, "
    qr += "data1.d_p as p1, data2.d_p as p2, c_class "
    qr += "FROM `rdf_data` as data1 "
    qr += "inner join rdf_data as data2 on data1.d_r1 = data2.d_r2 "
    qr += "inner join rdf_concept ON data1.d_r1 = id_cc "
    qr += "inner join rdf_class ON cc_class = id_c "
    qr += "where data1.d_r2 = data2.d_r1 "
    qr += "and data1.d_p = data2.d_p; "

def checkLiteralExist():
    qr = "select * from brapci_rdf.rdf_data"
    qr += " where "
    qr += " d_r2 = 0 "
    qr += " and d_p <> 0"
    qr += " and d_r1 > 0"
    qr += " and d_literal > 0"
    qr += " and d_trust = 0"
    qr += " limit 100000"
    print('103a - Liberando entradas Literais')
    row = database.query(qr)

    for item in row:
        ID = item[0]
        qu = "update brapci_rdf.rdf_data "
        qu += " set d_trust = 1 "
        qu += f" where id_d = {ID}"
        database.update(qu)
        print(f"Atualizado Literal Data - {ID}")

def checkDataConceptExist():
    print("Checando relações Orfã")
    qr = "select id_d, d_r1, d_r2 FROM brapci_rdf.rdf_data "
    qr += "left join brapci_rdf.rdf_concept ON d_r2 = id_cc "
    qr += "where d_trust = 0 and id_cc and d_r2 <> 0 is null limit 10 "
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
