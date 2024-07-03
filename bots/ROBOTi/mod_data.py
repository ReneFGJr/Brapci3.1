import re
import json
import string
import array
from colorama import Fore
import mod_listidentify
import mod_literal
import mod_concept
import mod_class
import database
import time
import mod_logs
import mod_ai_nlp

def literal_double(prop = 0):
    print("113/114 - Titulo ou Resumo duplicado")
    qr = "select * from ( "
    qr += "SELECT d_r1, n_lang, count(*) as total  FROM brapci_rdf.rdf_data "
    qr += "INNER JOIN brapci_rdf.rdf_literal ON d_literal = id_n "
    qr += f"WHERE `d_r2` = 0 AND d_p = {prop} "
    qr += "group by d_r1, n_lang "
    qr += ") as tabela  "
    qr += "WHERE total > 1;"

    row = database.query(qr)
    if row != []:
        for item in row:
            ID = item[0]
            lang = item[1]
            qn = "select d_r1, n_name, n_lang, id_d from brapci_rdf.rdf_data "
            qn += " inner join brapci_rdf.rdf_literal ON id_n = d_literal "
            qn += f" where d_r1 = {ID} and d_p = {prop} and n_lang = '{lang}' "
            qn += " order by id_d "
            row2 = database.query(qn)
            pha = ['','']
            n = 0
            for item2 in row2:
                IDd = item2[3]
                print("Item2",">>")
                print(item2)
                pha[n] = item2[1]
                n += 1
                if n > 2:
                    break

            print("==>",pha)


def invert():
    print("155 - Invertendo Propriedades")
    qr = "select id_d, d_r1, d_r2 from brapci_rdf.rdf_data "
    qr += f"where d_trust = -1 and d_literal = 0 and d_r2 > 0 "
    qr += "and ("
    qr += " (d_p = 33 and d_r1 = 9 and d_r2 = 10 ) "
    qr += " or (d_p = 33 and d_r1 = 9 and d_r2 = 16 ) "
    qr += " or (d_p = 33 and d_r1 = 9 and d_r2 = 7 ) "
    qr += " or (d_p = 33 and d_r1 = 9 and d_r2 = 6 ) "
    qr += " ) "

    row = database.query(qr)
    if row != []:
        for item in row:
            id_d = item[0]
            d_r1 = item[1]
            d_r2 = item[2]
            qu = f"update brapci_rdf.rdf_data set d_r1 = {d_r2}, d_r2 = {d_r1}, d_trust = 0 where id_d = {id_d}"
            database.update(qu)
            print("Revert",id_d)
    qd = "COMMIT"
    database.update(qd)

def revalid():
    print("157 - Invertendo Propriedades")
    qr = "select id_d, d_r1, d_r2 from brapci_rdf.rdf_data "
    qr += f"where d_trust = -1 and d_literal = 0 and d_r2 > 0 "

    row = database.query(qr)
    if row != []:
        for item in row:
            id_d = item[0]
            d_r1 = item[1]
            d_r2 = item[2]
            qu = f"update brapci_rdf.rdf_data set d_trust = 0 where id_d = {id_d}"
            database.update(qu)
            print("Revalid",id_d)
    qd = "COMMIT"
    database.update(qd)


def register(IDC,prop,IDP,IDliteral=0,ia=0):
    IDprop = mod_class.getClass(prop)

    qr = "select * from brapci_rdf.rdf_data "
    qr += f"where d_r1 = {IDC}"
    qr += f" AND d_p = {IDprop}"
    qr += f" AND d_r2 = {IDP}"
    qr += f" AND d_literal = {IDliteral}"
    row = database.query(qr)
    if row == []:
        qri = "insert into brapci_rdf.rdf_data "
        qri += "(d_r1, d_r2, d_p, d_literal,d_ia)"
        qri += " values "
        qri += f"({IDC},{IDP},{IDprop},{IDliteral},{ia});"
        database.insert(qri)
        row = database.query(qr)
    qd = "COMMIT"
    database.update(qd)

    return row

def invert_class(C1,C2,DP):
    qr = f"select id_d, d_r1, d_r2, d_c1, d_c2 from brapci_rdf.rdf_data where d_c1 = {C1} and d_c2 = {C2} and d_p and d_r2 > 0 "
    row = database.query(qr)
    for item in row:
        ID = item[0]
        R1 = item[1]
        R2 = item[2]
        C1 = item[3]
        C2 = item[4]

        qu = f"update brapci_rdf.rdf_data set d_trust = 0, d_r1 = {R2}, d_r2 = {R1}, d_c1 = {C2}, d_c2 = {C1} where id_d = {ID}"
        print("Invert class",ID)
        database.update(qu)
    qd = "COMMIT"
    database.update(qd)

def remicive(ID1,ID2):
    if ID1 == 0: return ""
    if ID2 == 0: return ""
    qr = [
        f"update brapci_rdf.rdf_data set d_r1 = {ID1} where d_r1 = {ID2}",
        f"update brapci_rdf.rdf_data set d_r2 = {ID1} where d_r2 = {ID2}",
        f"update brapci_rdf.rdf_concept set cc_use = {ID1} where id_cc = {ID2}"
    ]
    for qrt in qr:
        database.update(qrt)

    qd = "COMMIT"
    database.update(qd)

def register_literal(IDC,prop,name,lang):
    IDprop = mod_class.getClass(prop)
    IDliteral = mod_literal.register(name,lang)

    qr = "select * from brapci_rdf.rdf_data "
    qr += f"where d_r1 = {IDC}"
    qr += f" AND d_p = {IDprop}"
    qr += f" AND d_literal = {IDliteral}"
    row = database.query(qr)
    if row == []:
        qri = "insert into brapci_rdf.rdf_data "
        qri += "(d_r1, d_r2, d_p, d_literal)"
        qri += " values "
        qri += f"({IDC},0,{IDprop},{IDliteral});"
        database.insert(qri)
        row = database.query(qr)

    return row

def DataDouble():
    print("100 - Check duplicate")

    qr = "select * FROM ("
    qr += "SELECT max(id_d) as max, count(*) as total, d_r1,d_p,d_r2,d_literal "
    qr += "FROM brapci_rdf.rdf_data "
    qr += "group by d_r1,d_p,d_r2,d_literal "
    qr += ") T1 "
    qr += "where total > 1 "
    #qr += "limit 1000 "
    row = database.query(qr)
    dd = 0

    for l in row:
        ida = l[0]
        total = l[1]
        ID = str(l[2])
        print(Fore.YELLOW+"... Excluindo dados duplicados "+Fore.GREEN+str(ida)+','+str(total)+", ID:"+ID+Fore.WHITE)
        qd = f"delete from brapci_rdf.rdf_data where id_d = {ida}"
        dd = dd + 1
        database.update(qd)
        time.sleep(0.01)

    qd = "COMMIT"
    database.update(qd)
    mod_logs.log('TASK_100',dd)
