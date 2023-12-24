# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-24
# @Title: Modulo de listidentify

import database
import datetime
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
        qi += f"({update},1,{jnl},"
        qi += f"{issue}, '{id}','{stamp}',"
        qi += f"{setSpec}, {deleted},0"
        qi += ")"
        database.query(qi)
        print(Fore.BLUE+"... Inserido "+id+Fore.WHITE)
    else:
        print(Fore.YELLOW+"... Já existe "+Fore.GREEN+id+Fore.WHITE)

    return True
