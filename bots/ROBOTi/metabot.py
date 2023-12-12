#MetaBot
#Robo de colheira de Metadados

import oaipmh
import datetime
import brapci_base

def version():
    verstion = 'v. 23.11.25'
    return "MetaBot - "+verstion

def clearMarkup():
    brapci_base.clearMarkup()

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
        ID = ROW[0]
        #brapci_base.updateOaiIdentify(ID)
        return ID
    except:
        print("ROBOTi - [ERRO] getNextListIdentifier")
        return 0

def getIDENTIFY():
    print(version())
    print("getIDENTIFY")