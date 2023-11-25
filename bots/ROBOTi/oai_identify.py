import mysql.connector
from mysql.connector import errorcode
import xmltodict
import lib_oai_brapci
from sickle import Sickle

def harvesting(ID:str, URL:str):

    #Registra Log de início de colheita
    lib_oai_brapci.oai_log_register(ID,"Identify","1")

    ####################################### Get XML from OAI-PMH
    try:
        sickle = Sickle(URL)
    except:
        print("ERRO DE COLETA DA URL")
        print(URL)
        lib_oai_brapci.jnl_oai_status(ID,"500")
        lib_oai_brapci.oai_log_register(ID,"Identify","500")

    ####################################### Read XML File
    try:
        identify = str(sickle.Identify())
        identify = identify.replace('xmlns="http://www.openarchives.org/OAI/2.0/oai-identifier"','')
        identify = identify.replace('xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"','')
        identify = identify.replace('xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai-identifier','')
        identify = identify.replace('http://www.openarchives.org/OAI/2.0/oai-identifier.xsd"','')
        identify = identify.replace(' >','>')
        identify = identify.replace(' >','>')
        identify = identify.replace(' >','>')
        identify = identify.replace(' >','>')
        identify = identify.replace(' >','>')
        identify = identify.replace(' >','>')
        identify = identify.replace(' >','>')
        identify = identify.replace(' >','>')
        identify = identify.replace(' >','>')
    except:
        print("ERRO DE COLETA")
        print(URL)
        print(ID)
        #print(identify)

        print("##############")
        lib_oai_brapci.jnl_oai_status(ID,"404")
        lib_oai_brapci.oai_log_register(ID,"Identify","404")
        return ""

    ######################################### Read XML
    try:
        doc = xmltodict.parse(identify)
        ###################################### Select Database
        doc['id_jnl'] = ID
        lib_oai_brapci.identify_register(doc)
    except:
        print("Erro no formato do arquivo Identify")
        print(identify)
        #Registra Log de fim de colheita
    finally:
        lib_oai_brapci.oai_log_register(ID,"Identify","2")
        ## FIM
