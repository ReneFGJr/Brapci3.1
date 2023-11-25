import mysql.connector
from mysql.connector import errorcode
import xmltodict
import lib_oai_brapci
from sickle import Sickle
import oaipmh
import lib_oai_brapci

def harvesting(ID:str, URL:str):

    #Registra Log de início de colheita
    lib_oai_brapci.oai_log_register(ID,"Identify","1")

    try:
        oaipmh.url(URL)
        xml = oaipmh.identify()
    except:
        lib_oai_brapci.jnl_oai_status(ID,"500")
        lib_oai_brapci.oai_log_register(ID,"Identify","500")
        return "ERRO #1"
    finally:
        print("OK Coletado URL")
        print("====================")
        print(type(xml))
        print("Length:"+str(len(xml)))

    ######################################### Read XML
    try:
        doc = xmltodict.parse(xml)
    except:
        print("Erro ao Abrir o XML")

    ###################################### Select Database
    try:
        lib_oai_brapci.identify_register(doc,ID)
    except:
        fff = open('f.xml','w')
        write(fff,xml)
        fff.close()
        print("Erro no formato do arquivo Identify")
        print(identify)
        #Registra Log de fim de colheita
    finally:
        lib_oai_brapci.oai_log_register(ID,"Identify","2")
        ## FIM
