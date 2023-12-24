# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-24
# @Title: Modulo de setSpec

import database
import time
from colorama import Fore

table = "brapci_oaipmh.oai_setspec"

def register(id,jnl):
    qr = f"select * from {table} "
    qr += "where "
    qr += f"s_id = '{id}' "
    qr += f"and s_id_jnl = '{jnl}' "

    if (id == ''):
        print(Fore.RED+"ID não especificado"+Fore.WHITE)
        quit()

    row = database.query(qr)
    if row == []:
        qi = f"insert into {table} "
        qi += "(s_id,s_id_jnl,s_ignore)"
        qi += " values "
        qi += f"('{id}',{jnl},0)"
        row = database.query(qi)
        print(Fore.YELLOW+"... setSpec: "+Fore.GREEN+f" Novo setSpec {id} (JNL:{jnl})")
        time.sleep(0.5)
        row = database.query(qr)

    if (row == []):
        print(Fore.RED+"ERRO DE GRAVAÇÂO NO BANCO DE DADOS"+Fore.WHITE)
        quit()
    idset = row[0][0]
    return idset
    print(row)
    quit()

def process(sets,regs):
    jnl = regs[0][0]
    setsP = {}

    if sets['status']:
        for setSpec in sets['setSpec']:
            setsP[setSpec] = register(setSpec,jnl)
    return setsP
