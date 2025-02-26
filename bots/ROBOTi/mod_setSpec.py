# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-24
# @Title: Modulo de setSpec

import database
import time
from colorama import Fore

table = "brapci_oaipmh.oai_setspec"

def register(id,jnl,name):
    name = name.replace("'","´")
    id = id[0:255]
    qr = f"select id_s,s_id,s_id_jnl,s_name from {table} "
    qr += "where "
    qr += f"s_id = '{id}' "
    qr += f"and s_id_jnl = '{jnl}' "

    if (id == ''):
        print(Fore.RED+"ID não especificado"+Fore.WHITE)
        quit()

    row = database.query(qr)
    if row == []:
        qi = f"insert into {table} "
        qi += "(s_id,s_id_jnl,s_name,s_ignore)"
        qi += " VALUES "
        qi += f"('{id}',{jnl},'{name}',0)"
        row = database.insert(qi)
        print(Fore.YELLOW+"... setSpec: "+Fore.GREEN+f" Novo setSpec {id} (JNL:{jnl})")
        time.sleep(0.5)
        row = database.query(qr)
    else:
        if row[0][3] == '':
            qu = f"update {table} set "
            qu += f" s_name = '{name}' "
            qu += "where "
            qu += f"s_id = '{id}' "
            qu += f"and s_id_jnl = '{jnl}' "
            database.update(qu)
            print(Fore.YELLOW+"... setSpec: "+Fore.BLUE+f" Atualizado setSpec {id} (JNL:{jnl})")


    if (row == []):
        print(Fore.RED+"ERRO DE GRAVAÇÂO NO BANCO DE DADOS"+Fore.WHITE)
        print(Fore.BLUE+qi+Fore.WHITE)
        quit()
    idset = row[0][0]
    return idset

def getSetSpec(JNL):
    qr = "select s_id_jnl, s_section, s_id, s_name  from "+table+" "
    qr += f"where s_id_jnl = {JNL}"
    row = database.query(qr)
    return row

def process(sets,regs):
    jnl = regs[0][0]
    name = regs[0][3]
    setsP = {}

    if sets['status']:
        for setSpec in sets['setSpec']:
            setsP[setSpec] = register(setSpec,jnl,name)
    return setsP
