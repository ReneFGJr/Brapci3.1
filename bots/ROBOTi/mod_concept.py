import database
import mod_logs

def remissives():
    qr = "SELECT id_cc, cc_use FROM brapci_rdf.rdf_concept "
    qr += " INNER JOIN brapci_rdf.rdf_data ON id_cc = d_r1 "
    qr += " WHERE cc_use <> id_cc "
    qr += " group by id_cc, cc_use "
    row = database.query(qr)

    print(row)

    for ln in row:
        print(ln)
        ID = ln[0]
        USE = ln[1]
        qu = f"update brapci_rdf.rdf_data set d_r1 = {USE} where d_r1 = {ID}"
        print(qu)


def UpdateUse():
    print("000 - Update USE")
    qu = "update brapci_rdf.rdf_concept set cc_use = id_cc where cc_use = 0"
    database.update(qu)

    qd = "COMMIT"
    database.update(qd)

    mod_logs.log('TASK_000',0)

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