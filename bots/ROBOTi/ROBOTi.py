import sys
import env
import brapci_base

def version():
    return "v0.23.12.01"
def Identify():
    import metabot
    metabot.getNextIdentify()
def Listidentifiers():
    import metabot
    ID = metabot.getNextListIdentifier()
    if (ID > 0):
        brapci_base.getListIdentifier(ID)


print("ROBOTi",version())
print("=====================")

if (len(sys.argv) > 1):
    parm = sys.argv
    if (parm[1] == '--help'):
        brapci_base.help_roboti()
    elif ((parm[1] == 'identify') or (parm[1] == bytearray(b'identify'))):
        print("Verb: Identify")
        Identify()
    elif ((parm[1] == 'Listidentifiers') or (parm[1] == bytearray(b'Listidentifiers'))):
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
