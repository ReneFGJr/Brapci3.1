from mysql.connector import MySQLConnection, Error
from mysql.connector import errorcode
import brapci_base
import xmltodict
import datetime
import requests

#MODULO - OAIPHM
#AUTHOR: Rene Faustino Gabriel Junior
#DATA: 2023-12-01
#VERSÂO: 2


URL = "https://"

def url(LINK:str):
    global URL
    URL = LINK

def ListIdentifiers(url,token):
    LINK = url + '?verb=ListIdentifiers&metadataPrefix=oai_dc'
    print(f"... Recuperando {LINK} - OAIPMH - LisyIdentifiers")
    try:
        cnt = requests.get(LINK,verify=False, timeout=30.0)
    except requests.exceptions.SSLError:
        pass
    except:
        print(cnt.status_code)
        print(f"... Erro request - OAIPMH - LisyIdentifiers")
        return ""
    finally:
        try:
            return cnt.text
        except:
            print(f"... cnt.text empty - OAIPMH - LisyIdentifiers")
            return ""

def identify():
    LINK = URL + '?verb=Identify'
    print(f"... Recuperando {LINK} - OAIPMH - Identify")
    try:
        cnt = requests.get(LINK,verify=False, timeout=2.5)
    except requests.exceptions.SSLError:
        pass
    except:
        print(cnt.status_code)
        print(f"... Erro request - OAIPMH - Identify")
        return ""
    finally:
        try:
            return cnt.text
        except:
            print(f"... cnt.text empty - OAIPMH - Identify")
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
        print("ERRO OAIPHM-getIdentify-len")
        print(xml)

######################################### IdentifyProcess
def IdentifyProcess(ID,xml):
    ######################################### Read XML
    try:
        doc = xmltodict.parse(xml)
        print("HELLO PRP")
        brapci_base.identify_register(ID,xml)
        print("XML OK")
        return True
    except:
        print("Erro ao Abrir o XML")
        print(xml)
        return False


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
