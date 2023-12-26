# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-24
# @Title: Modulo de Source

import database
import xmltodict
import datetime

from colorama import Fore

table = "brapci.source_source"

def update(jnl,status,token):
    update = datetime.datetime.now().strftime('%Y%m%d')
    now = datetime.datetime.now().strftime('%Y-%m-%d')

    if not token == '':
        qr = f"update {table} set "
        qr += f"jnl_oai_token = '{token}', "
        qr += f"jnl_oai_status = '{status}' "
        qr += f" where id_jnl = {jnl}"
        database.update(qr)
    else:
        qr = f"update {table} set "
        qr += f"jnl_oai_token = '', "
        qr += f"update_at = '{update}', "
        qr += f"jnl_oai_last_harvesting = '{now}' "
        qr += f" where id_jnl = {jnl}"
        database.update(qr)

def token(xml):
    xml = xml['content']
    doc = xmltodict.parse(xml)
    doc = doc['OAI-PMH']
    doc = doc['ListIdentifiers']

    try:
        token = doc['resumptionToken']
        token = token['#text']
    except:
        token = ''
    return token