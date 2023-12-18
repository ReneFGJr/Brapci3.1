import sys
import env
import brapci_base

def version():
    return "v0.23.12.12"
def ClearMarkup():
    import metabot
    metabot.clearMarkup()
def Identify():
    import metabot
    metabot.getNextIdentify()
def GetRecord():
    import metabot
    metabot.GetRecord()
def processRecord():
    import metabot
    metabot.processRecord()

def Listidentifiers():
    import metabot
    import oaipmh
    ID = metabot.getNextListIdentifier()
    if (ID > 0):
        loop = 1
        while loop == 1:
            row = brapci_base.getListIdentifier(ID)
            if (row[0] != ''):
                try:
                    if (row[1] == None):
                        row[1] = ''
                    xml = oaipmh.ListIdentifiers(row[0],row[1])
                    brapci_base.zeraToken(ID)
                    if (xml != ''):
                        loop = brapci_base.processListIdentifiers(ID,xml)
                except:
                    loop = 0
                    print("Nada para processar - ListIdentifiers")
            else:
                loop = 0
                print("Nada para processar - ListIdentifiers")

########################################### Início
print("ROBOTi",version())
print("=====================")

if (len(sys.argv) > 1):
    parm = sys.argv
    if (parm[1] == '--help'):
        brapci_base.help_roboti()
    elif ((parm[1] == 'clear') or (parm[1] == bytearray(b'clear'))):
        print("Verb: Clear Markups")
        ClearMarkup()
    elif ((parm[1] == 'identify') or (parm[1] == bytearray(b'identify'))):
        print("Verb: Identify")
        Identify()
    elif ((parm[1] == 'getrecord') or (parm[1] == bytearray(b'getrecord'))):
        print("Verb: GetRecord")
        GetRecord()
    elif ((parm[1] == 'process') or (parm[1] == bytearray(b'process'))):
        print("Verb: Process Register")
        processRecord()
    elif ((parm[1] == 'listidentifiers') or (parm[1] == bytearray(b'listidentifiers'))):
        print("Verb: Listidentifiers")
        Listidentifiers()
    elif(parm[1] == 'run'):
        ####################################### NEXT
        act = brapci_base.next_action()

        ####################################### GET
        if (act == 'none'):
            print("Sem ações agendada")
        elif ((act == 'oai_identifty') or (act == bytearray(b'oai_identifty'))):
            Identify()
        elif ((act == 'oai_listidentifiers') or (act == bytearray(b'oai_listidentifiers'))):
            Listidentifiers()
        elif ((act == 'translate') or (act == bytearray(b'translate'))):
            import translate
            translate.translateNext()
        else:
            print(f".ROBOTi - Action not recognized {act}")
    elif(parm[1] == 'dbtest'):
        ID = brapci_base.dbtest()
    elif(parm[1] == 'status'):
        print("STATUS: OK")
else:
    print("Argumentos não informado, veja ROBOTi --help")
