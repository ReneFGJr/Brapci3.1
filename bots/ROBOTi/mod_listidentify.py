# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-24
# @Title: Modulo de listidentify

from colorama import Fore
import database
import datetime
import os
import sys


table = "brapci_oaipmh.oai_listidentify"

def registers(ids,jnl,issue=0):
    for idr in ids:
        setspec = ids[idr]['setSpec']
        date = ids[idr]['date']
        deleted = ids[idr]['deleted']
        register(idr,jnl,setspec,date,deleted,issue)
    return True

def register(id_reg,jnl,setSpec,stamp,deleted,issue=0):
    status = 1
    if (deleted == 1):
        status = 9

    # *********** ISSUE
    qr = "select * from brapci_oaipmh.oai_setspec "
    qr += f" where (s_id = '{setSpec}' or id_s = '{setSpec}') "
    qr += f" and s_id_jnl = '{jnl}' "
    set = database.query(qr)

    if set != []:
        idsetSpec = set[0][0]

        qr = "select * "
        qr += f"from {table} "
        qr += "where "
        qr += f" (oai_identifier = '{id_reg}') "
        qr += f"and (oai_setSpec = '{idsetSpec}') "
        qr += f"and (oai_id_jnl = {jnl})"
        row = database.query(qr)

        update = datetime.datetime.now().strftime('%Y%m%d')

        stamp = stamp.replace('T',' ')
        stamp = stamp.replace('Z','')

        if row == []:
            qi = f"insert into {table} \n"
            qi += "(oai_update, oai_status, oai_id_jnl, "
            qi += "oai_issue, oai_identifier, oai_datestamp, "
            qi += "oai_setSpec, oai_deleted, oai_rdf"
            qi += ") \n"
            qi += " values \n"
            qi += f"({update},{status},{jnl},"
            qi += f"{issue}, '{id_reg}','{stamp}',"
            qi += f"{idsetSpec}, {deleted},0"
            qi += ")"
            database.insert(qi)
            print(Fore.YELLOW+"... Inserido "+Fore.GREEN+id_reg+Fore.WHITE)
        else:
            deleted_db = row[0][8]
            id_oai = row[0][0]
            if (deleted != deleted_db):
                qu = f"update {table} set "
                qu += f"oai_deleted = {deleted}, "
                qu += f"oai_status = {status} "
                qu += f"where id_oai = {id_oai} "
                database.update(qu)
                print(Fore.YELLOW+"... atualizado "+Fore.GREEN+id_reg+Fore.WHITE)
            else:
                print(Fore.BLUE+"... Já existe "+Fore.GREEN+id_reg+Fore.WHITE)
    return True

def updateRDF(ID,rdf):
    try:
        update = datetime.datetime.now().strftime('%Y%m%d')
        now = datetime.datetime.now().strftime('%Y-%m-%d')

        qr = f"update {table} set "
        qr += f"oai_rdf = {rdf}, "
        qr += f"oai_update = {now} "
        qr += f"where id_oai = {ID} "
        database.update(qr)

    except Exception as e:
        print("ERRO #24",e)

def updateIssue(ID,issue):
    try:
        update = datetime.datetime.now().strftime('%Y%m%d')
        now = datetime.datetime.now().strftime('%Y-%m-%d')

        qr = f"update {table} set "
        qr += f"oai_issue = {issue}, "
        qr += f"oai_update = {now} "
        qr += f"where id_oai = {ID} "
        database.update(qr)
    except Exception as e:
        print("ERRO #23",e)

def updateRDFid(ID,IDC):
    try:
        print("Atualizando")
        qr = f"update {table} set "
        qr += f"oai_rdf = {IDC} "
        qr += f"where id_oai = {ID} "
        database.update(qr)
    except:
        print("ERRO ao gravar ",qr)

def updateStatus(ID,status):
    update = datetime.datetime.now().strftime('%Y%m%d')
    now = datetime.datetime.now().strftime('%Y-%m-%d')

    qr = f"update {table} set "
    qr += f"oai_status = {status}, "
    qr += f"oai_update = {now} "
    qr += f"where id_oai = {ID} "
    database.update(qr)

def directory(id):
    tp = str(id)
    while len(tp) < 10:
        tp = "0"+tp
    tp1 = tp[0:4]
    tp2 = tp[4:8]

    if not os.path.isdir('../../public/_repository'):
        os.mkdir('../../public/_repository')
    if not os.path.isdir('../../public/_repository/oai'):
        os.mkdir('../../public/_repository/oai')
    if not os.path.isdir('../../public/_repository/oai/'+tp1):
        os.mkdir('../../public/_repository/oai/'+tp1)
    if not os.path.isdir('../../public/_repository/oai/'+tp1+'/'+tp2):
        os.mkdir('../../public/_repository/oai/'+tp1+'/'+tp2)
    dir = f"../../public/_repository/oai/{tp1}/{tp2}/{tp}"
    return dir