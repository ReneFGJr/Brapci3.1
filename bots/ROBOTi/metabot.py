#MetaBot
#Robo de colheira de Metadados

import oaipmh
import datetime
import brapci_base
import os

def version():
    verstion = 'v. 23.11.25'
    return "MetaBot - "+verstion

def clearMarkup():
    brapci_base.clearMarkup()

################################################### CACHED FILE
def cached(id):
    file = directory(id)+'.getRecord.xml'
    if os.path.exists(file):
        return True
    else:
        return False

def directory(id):
    tp = str(id)
    while len(tp) < 10:
        tp = "0"+tp
    tp1 = tp[0:4]
    tp2 = tp[4:8]

    if not os.path.isdir('../../.tmp'):
        os.mkdir('../../.tmp')
    if not os.path.isdir('../../.tmp/oai'):
        os.mkdir('../../.tmp/oai')
    if not os.path.isdir('../../.tmp/oai/'+tp1):
        os.mkdir('../../.tmp/oai/'+tp1)
    if not os.path.isdir('../../.tmp/oai/'+tp1+'/'+tp2):
        os.mkdir('../../.tmp/oai/'+tp1+'/'+tp2)
    dir = f"../../.tmp/oai/{tp1}/{tp2}/{tp}"
    return dir

def cache_file_save(id,txt):
    file = directory(id)+'.getRecord.xml'
    f = open(file, "w")
    f.write(txt)
    f.close()

def readfile(id):
    file = directory(id)+'.getRecord.xml'
    f = open(file, "r")
    xml = f.read()
    return xml

################################################### GET RECORD
def GetRecord():
    # Busca proxima coleta
    id_reg = brapci_base.getNextRegister(1)
    #brapci_base.updateRegisterStatus(id_reg,5)
    brapci_base.updateRegisterStatus(id_reg,2)
    if id_reg > 0:
        if (cached(id_reg)):
            xml = readfile(id_reg)
        else:
            xml = brapci_base.getRegister(id_reg)
            cache_file_save(id_reg,xml)
        print("===============",len(xml),id_reg)
        ######################## SAVE CACHE
        if (len(xml) > 0):
            brapci_base.updateRegisterStatus(id_reg,5)
    else:
        print("Nengum registro para coletar")

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
        return ID
    except:
        print("ROBOTi - [ERRO] getNextListIdentifier")
        return 0

def getIDENTIFY():
    print(version())
    print("getIDENTIFY")