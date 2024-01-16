# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-24
# @Title: Modulo de listidentify

import database
import datetime
import os
from colorama import Fore

table = "brapci_oaipmh.oai_listidentify"

def registers(ids,jnl):
    for idr in ids:
        setspec = ids[idr]['setSpec']
        date = ids[idr]['date']
        deleted = ids[idr]['deleted']
        register(idr,jnl,setspec,date,deleted)
    return True

def register(id,jnl,setSpec,stamp,deleted):
    status = 1
    if (deleted == 1):
        status = 9

    qr = "select * "
    qr += f"from {table} "
    qr += "where "
    qr += f" (oai_identifier = '{id}') "
    qr += f"and (oai_setSpec = '{setSpec}') "
    row = database.query(qr)

    issue = 0
    update = datetime.datetime.now().strftime('%Y%m%d')

    stamp = stamp.replace('T',' ')
    stamp = stamp.replace('Z','')

    if row == []:
        qi = f"insert into {table} "
        qi += "(oai_update, oai_status, oai_id_jnl, "
        qi += "oai_issue, oai_identifier, oai_datestamp, "
        qi += "oai_setSpec, oai_deleted, oai_rdf"
        qi += ")"
        qi += " values "
        qi += f"({update},{status},{jnl},"
        qi += f"{issue}, '{id}','{stamp}',"
        qi += f"{setSpec}, {deleted},0"
        qi += ")"
        database.insert(qi)

        print(Fore.YELLOW+"... Inserido "+Fore.GREEN+id+Fore.WHITE)
    else:
        deleted_db = row[0][8]
        id_oai = row[0][0]
        if (deleted != deleted_db):
            qu = f"update {table} set "
            qu += f"oai_deleted = {deleted}, "
            qu += f"oai_status = {status} "
            qu += f"where id_oai = {id_oai} "
            database.update(qu)
            print(Fore.YELLOW+"... atualizado "+Fore.GREEN+id+Fore.WHITE)
        else:
            print(Fore.BLUE+"... Já existe "+Fore.GREEN+id+Fore.WHITE)
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