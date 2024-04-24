import database
import mod_logs
import mod_data

def resume():
    qr = "SELECT count(*) as total, d_trust "
    qr += " FROM brapci_rdf.rdf_data "
    qr += " group by d_trust"
    row = database.query(qr)
    for item in row:
        print(item)

def classification():
    qr = " SELECT id_d, "
    qr += "d_r1 as R1, c1.cc_class as C1, "
    qr += "d_r2 as R2, c2.cc_class as C2 "
    qr += "FROM brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_concept as c1 ON d_r1 = c1.id_cc "
    qr += "left join brapci_rdf.rdf_concept as c2 ON d_r2 = c2.id_cc "
    qr += "WHERE d_c1 = 0 "
    qr += "limit 100000 "
    row = database.query(qr)

    for item in row:
        id_d = item[0]
        c1 = item[2]
        c2 = item[4]
        if (c2 == None):
            c2 = 0
        qu = "update brapci_rdf.rdf_data "
        qu += "set "
        qu += f" d_c1 = {c1}, "
        qu += f" d_c2 = {c2}, "
        qu += f" d_trust = 0 "
        qu += f" where id_d = {id_d}"
        print("Classification ",id_d,c1,c2)
        database.update(qu)

def checkDataInverse():
    qr = "SELECT d_c1, d_c2, d_p, count(*) as total FROM brapci_rdf.rdf_class_domain "
    qr += "inner join brapci_rdf.rdf_data ON cd_domain= d_c2 and cd_range = d_c1 and cd_property = d_p "
    qr += "group by d_c1, d_c2, d_p"
    print("101 - Checando Invertidas")

    row = database.query(qr)

    qu = ""
    n = 0
    dd = 0
    for item in row:
        print(item)
        C1 = item[0]
        C2 = item[1]
        DP = item[2]
        TOTAL = item[3]

        mod_data.invert_class(C1,C2,DP)
        dd = dd + TOTAL
    mod_logs.log('TASK_101',dd)

def checkLiteralExist():
    qr = "select * from brapci_rdf.rdf_data"
    qr += " where "
    qr += " d_r2 = 0 "
    qr += " and d_p <> 0"
    qr += " and d_r1 > 0"
    qr += " and d_literal > 0"
    qr += " and d_trust = 0"
    qr += " limit 100000"
    print('102 - Validando as entradas (Trust) entradas Literais')
    row = database.query(qr)
    ini = 0
    for item in row:
        ini = ini + 1
        if ini > 100:
            ini = 0
            print(".",end='')
        ID = item[0]
        qu = "update brapci_rdf.rdf_data "
        qu += " set d_trust = 1 "
        qu += f" where id_d = {ID}"
        database.update(qu)

def checkDataConceptExist():
    print("Checando relações Orfã")
    qr = "select id_d, d_r1, d_r2 FROM brapci_rdf.rdf_data "
    qr += "left join brapci_rdf.rdf_concept ON d_r2 = id_cc "
    qr += "where d_trust = 0 and id_cc and d_r2 <> 0 is null limit 10 "
    print("103 - Checando Existencia das relacoes R2")
    row = database.query(qr)
    print(row)

def checkData():
    qr = "SELECT d_c1, d_c2, d_p, count(*) as total FROM brapci_rdf.rdf_class_domain "
    qr += "inner join brapci_rdf.rdf_data ON cd_domain= d_c1 and cd_range = d_c2 and cd_property = d_p "
    qr += "WHERE d_trust = 0 "
    qr += "group by d_c1, d_c2, d_p"

    print("110 - Checando Ontologias")

    row = database.query(qr)

    qu = ""
    n = 0
    dd = 0
    for item in row:
        C1 = item[0]
        C2 = item[1]
        DP = item[2]
        TOTAL = item[3]

        qu = f"update brapci_rdf.rdf_data set d_trust = 1 where d_c1 = {C1} and d_c2 = {C2} and d_p= {DP}"
        n = n + 1
        dd = dd + 1
        database.update(qu)
        print("Ontology Trust",C1,C2,DP,'Total:',TOTAL)
    mod_logs.log('TASK_110',dd)

def checkDataNull():
    qr = "SELECT d_c1, d_c2, d_p, count(*) as total FROM brapci_rdf.rdf_class_domain "
    qr += "left join brapci_rdf.rdf_data ON cd_domain= d_c1 and cd_range = d_c2 and cd_property = d_p "
    qr += "WHERE d_trust = 0 and id_cd = null "
    qr += "group by d_c1, d_c2, d_p"

    print(qr)

    print("110 - Checando Ontologias Nulas")

    row = database.query(qr)

    qu = ""
    n = 0
    dd = 0
    for item in row:
        C1 = item[0]
        C2 = item[1]
        DP = item[2]
        TOTAL = item[3]

        qu = f"update brapci_rdf.rdf_data set d_trust = -1 where d_c1 = {C1} and d_c2 = {C2} and d_p= {DP}"
        n = n + 1
        dd = dd + 1
        database.update(qu)
        print("Ontology not Trust",C1,C2,DP,'Total:',TOTAL)
    mod_logs.log('TASK_111',dd)
