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
        identify = identify.replace('xsi:schemaLocation="http://oai.dlib.vt.edu/OAI/metadata/toolkit http://oai.dlib.vt.edu/OAI/metadata/toolkit.xsd"','')
    except:
        print("ERRO DE COLETA")
        print(URL)
        print(ID)

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
