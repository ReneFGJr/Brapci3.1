from mysql.connector import MySQLConnection, Error
from mysql.connector import errorcode
import brapci_base
import xmltodict
import datetime
import requests


URL = "https://"

def url(LINK:str):
    global URL
    URL = LINK

def identify():
    LINK = URL + '?verb=Identify'
    print(f"... Recuperando {LINK}")
    try:
        cnt = requests.get(LINK)
    except:
        return ""
    finally:
        try:
            return cnt.text
        except:
            return ""

def getIdentify(ID,URL):
    url(URL)
    xml = identify()
    if (len(xml) > 10):
        updateSource(ID,'500')
        if IdentifyProcess(ID,xml):
            updateSource(ID,'100')
        else:
            updateSource(ID,'501')
    else:
        print("ERRO")

######################################### IdentifyProcess
def IdentifyProcess(ID,xml):
    ######################################### Read XML
    try:
        doc = xmltodict.parse(xml)
        print("XML OK")
    except:
        print("Erro ao Abrir o XML")

def updateSource(ID,status):
    global sourceName
    now_time = datetime.datetime.now()
    #now_time = datetime.datetime.now().strftime('%Y-%m-%d')

    query = f"update \n"
    query += f"brapci.source_source set "
    query += f"jnl_oai_status = '{status}' \n"
    query += f", update_at = '{now_time}' \n "
    query += f"where (id_jnl = {ID}) \n"

    ##################### Conectado
    cnx = brapci_base.query(query)
