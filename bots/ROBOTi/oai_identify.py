import mysql.connector
from mysql.connector import errorcode
import xmltodict
import lib_oai_brapci
from sickle import Sickle

def harvesting(ID:str, URL:str):

    #Registra Log de início de colheita
    lib_oai_brapci.oai_log_register(ID,"Identify","1")

    ####################################### Get XML from OAI-PMH
    sickle = Sickle(URL)

    ####################################### Read XML File
    try:
        identify = str(sickle.Identify())

        ######################################### Read XML
        doc = xmltodict.parse(identify)

        ###################################### Select Database
        doc['id_jnl'] = ID
        lib_oai_brapci.identify_register(doc)
    except:
        #Registra Log de fim de colheita
        print("ERRO DE COLETA - "+ID)
        lib_oai_brapci.jnl_oai_status(ID,"404")
        lib_oai_brapci.oai_log_register(ID,"Identify","404")
    finally:
        lib_oai_brapci.oai_log_register(ID,"Identify","2")
        ## FIM