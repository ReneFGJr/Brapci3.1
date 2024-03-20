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

def register(IDC,prop,IDP):
    IDprop = mod_class.getClass(prop)
    IDliteral = 0

    qr = "select * from brapci_rdf.rdf_data "
    qr += f"where d_r1 = {IDC}"
    qr += f" AND d_p = {IDprop}"
    qr += f" AND d_r2 = {IDP}"
    row = database.query(qr)
    if row == []:
        qri = "insert into brapci_rdf.rdf_data "
        qri += "(d_r1, d_r2, d_p, d_literal)"
        qri += " values "
        qri += f"({IDC},{IDP},{IDprop},{IDliteral});"
        database.insert(qri)
        row = database.query(qr)

    return row

def remicive(ID1,ID2):
    qr = f"update brapci_rdf.rdf_data set d_r1 = ID1 where d_r1 = ID2"
    print(qr)
    qr = f"update brapci_rdf.rdf_data set d_r2 = ID1 where d_r2 = ID2"
    print(qr)



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
    qr = "select * FROM ("
    qr += "SELECT max(id_d) as max, count(*) as total, d_r1,d_p,d_r2,d_literal "
    qr += "FROM brapci_rdf.rdf_data "
    qr += "group by d_r1,d_p,d_r2,d_literal "
    qr += ") T1 "
    qr += "where total > 1 "
    qr += "limit 1000 "
    row = database.query(qr)

    for l in row:
        ida = l[0]
        total = l[1]
        ID = str(l[2])
        print(Fore.YELLOW+"... Excluindo dados duplicados "+Fore.GREEN+str(ida)+','+str(total)+", ID:"+ID+Fore.WHITE)
        qd = f"delete from brapci_rdf.rdf_data where id_d = {ida}"
        database.update(qd)
        time.sleep(0.01)
