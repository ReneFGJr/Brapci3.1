from mysql.connector import MySQLConnection, Error
from mysql.connector import errorcode
import brapci_base
import xmltodict
import datetime
import requests
import urllib3

urllib3.disable_warnings()

#MODULO - OAIPHM
#AUTHOR: Rene Faustino Gabriel Junior
#DATA: 2023-12-01
#VERSÂO: 2


URL = "https://"

def url(LINK:str):
    global URL
    URL = LINK

def ListIdentifiers(url,token):
    headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.75 Safari/537.36'}
    data = {'v': 1}
    if token != '':
        LINK = url + '?verb=ListIdentifiers&resumptionToken=' + token
    else:
        LINK = url + '?verb=ListIdentifiers&metadataPrefix=oai_dc'
    print(f"... Recuperando {LINK} - OAIPMH - LisyIdentifiers")
    try:
        cnt = requests.get(LINK,verify=False, data=data, timeout=90.0, headers=headers, allow_redirects=True)
        print(cnt.status_code)
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
    headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.75 Safari/537.36'}

    print(f"... Recuperando {LINK} - OAIPMH - Identify")
    try:
        cnt = requests.get(LINK,verify=False, timeout=2.5, headers=headers)
    except requests.exceptions.SSLError:
        pass
    except Exception as e:
        print("============================================")
        print(e)
        print(cnt.status_code)
        print(f"... Erro request - OAIPMH - Identify")
        return ""
    finally:
        try:
            return cnt.text
        except:
            print(cnt.text)
            print(f"... cnt.text empty - OAIPMH - Identify")
            return ""

def getIdentify(ID,URL):
    url(URL)
    try:
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
    except Exception as e:
        print("============================================")
        print(e)

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
