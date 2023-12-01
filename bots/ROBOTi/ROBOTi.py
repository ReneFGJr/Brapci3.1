import sys
import env
import brapci_base

def version():
    return "v0.23.12.01"

print("ROBOTi",version())
print("=====================")

if (len(sys.argv) > 1):
    parm = sys.argv
    if (parm[1] == '--help'):
        brapci_base.help_roboti()
    elif(parm[1] == 'run'):
        ####################################### NEXT
        act = brapci_base.next_action()

        ####################################### GET
        if (act == 'none'):
            print("Sem ações agendada")
        elif ((act == 'oai_identifty') or (act == bytearray(b'oai_identifty'))):
            import metabot
            metabot.getNextIdentify()
        elif ((act == 'oai_listidentifiers') or (act == bytearray(b'oai_listidentifiers'))):
            import metabot
            metabot.getNextListIdentifier()
        else:
            print(f".ROBOTi - Action not recognized {act}")
    elif(parm[1] == 'dbtest'):
        ID = brapci_base.dbtest()
    elif(parm[1] == 'status'):
        print("STATUS: OK")
else:
    print("Argumentos não informado, veja ROBOTi --help")
