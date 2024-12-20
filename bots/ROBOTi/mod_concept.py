import database
import mod_logs
import mod_class
import mod_literal
import sys

def remissives():
    print("000 - Update USE->ID (RD_1)")
    qr = "SELECT id_cc, cc_use FROM brapci_rdf.rdf_concept "
    qr += " INNER JOIN brapci_rdf.rdf_data ON id_cc = d_r1 "
    qr += " WHERE cc_use <> id_cc "
    qr += " group by id_cc, cc_use "
    row = database.query(qr)

    if row:
        size = len(row)
        print("Registros USE=>ID:", size)

        for ln in row:
            ID = ln[0]
            USE = ln[1]
            qu = f"update brapci_rdf.rdf_data set d_r1 = {USE} where d_r1 = {ID}"
            database.update(qu)

    print("000 - Update USE->ID (RD_2)")
    qr = "SELECT id_cc, cc_use FROM brapci_rdf.rdf_concept "
    qr += " INNER JOIN brapci_rdf.rdf_data ON id_cc = d_r2 "
    qr += " WHERE cc_use <> id_cc "
    qr += " group by id_cc, cc_use "
    row = database.query(qr)

    if row:
        size = len(row)
        print("Registros USE=>ID:", size)

        for ln in row:
            ID = ln[0]
            USE = ln[1]
            qu = f"update brapci_rdf.rdf_data set d_r2 = {USE} where d_r2 = {ID}"
            database.update(qu)

def UpdateUse():
    print("000 - Update USE")
    qu = "update brapci_rdf.rdf_concept set cc_use = id_cc where cc_use = 0"
    database.update(qu)

    qd = "COMMIT"
    database.update(qd)

    mod_logs.log('TASK_000',0)

def register_literal_class(classe,name,lang):
    classe = mod_class.getClass(classe)

    IDl = mod_literal.register(name,lang)
    IDc = register(classe,IDl)

    return IDc

def removeElastic():
    total = 0
    qu = f"select ID from brapci_elastic.dataset where  "
    # Benancib
    qu += " (JOURNAL = 75 and YEAR = 2023 and status = 9) "
    # PBCIB
    qu += " or (JOURNAL = 12 AND `use` = -99)"

    row = database.query(qu)
    for item in row:
        total = total + 1
        ID = item[0]
        print(item,ID)
        remove(ID)
        qr = f"delete from brapci_elastic.dataset where ID = '{ID}' "
        database.update(qr)
    print(f"Total de {total} itens removidos")
    return total

def remove(id):
    qu = f"delete from brapci_rdf.rdf_data where d_r1 = {id} or d_r2 = {id}"
    database.update(qu)

    qu = f"delete from brapci_rdf.rdf_concept where id_cc = {id}"
    database.update(qu)


def register(cl,literal):

    qr = "select * from brapci_rdf.rdf_concept "
    qr += f" where (cc_class = {cl}) and (cc_pref_term = {literal})"
    row = database.query(qr)

    if row == []:
        qri = 'insert into brapci_rdf.rdf_concept '
        qri += "(cc_class , cc_use , c_equivalent, cc_pref_term , cc_origin , cc_status , cc_version, cc_update )"
        qri += " values "
        qri += f"({cl},0,0,{literal},'',0,2,'2000-01-01')"
        database.query(qri)
        row = database.query(qr)

    qd = "COMMIT"
    database.update(qd)

    return row[0][0]