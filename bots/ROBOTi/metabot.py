#MetaBot
#Robo de colheira de Metadados

import oaipmh
import datetime
import brapci_base

def version():
    verstion = 'v. 23.11.25'
    return "MetaBot - "+verstion

def getNextIdentify():
    # ************************ Recupera proxima coleta *
    ID = brapci_base.getNextIdentify()
    print("====================")
    print(brapci_base.sourceName,'('+str(ID)+')')

    if (ID > 0):
        print("... Colentando " + brapci_base.URL)
        oaipmh.updateSource(ID,'404')
        oaipmh.getIdentify(ID,brapci_base.URL)
    else:
        print("getNextIdentify - Nada para coletar")

def getNextListIdentifier():
    ROW = brapci_base.getNextListIdentifier()
    try:
        print("=x=>")
        ID = ROW[0]
        print(ID)
        brapci_base.updateOaiIdentify(ID)
    except:
        print("ROBOTi - [ERRO] getNextListIdentifier")

def getIDENTIFY():
    print(version())
    print("getIDENTIFY")