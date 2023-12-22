import sys
import env
import bots.ROBOTi.OLD2.brapci_base as brapci_base
import bots

def version():
    return "v0.23.12.12"

def ClearMarkup():
    import metabot
    metabot.clearMarkup()


########################################### Início
print("ROBOTi",version())
print("===============================================")

if (len(sys.argv) > 1):
    parm = sys.argv
    if (parm[1] == '--help'):
        brapci_base.help_roboti()
    elif ((parm[1] == 'run') or (parm[1] == bytearray(b'run'))):
        bots.run()
    elif ((parm[1] == 'clear') or (parm[1] == bytearray(b'clear'))):
        print("Verb: Clear Markups")
        ClearMarkup()
    elif ((parm[1] == 'identify') or (parm[1] == bytearray(b'identify'))):
        print("Verb: Identify")
        bots.Identify()
    elif ((parm[1] == 'getrecord') or (parm[1] == bytearray(b'getrecord'))):
        print("Verb: GetRecord")
        bots.GetRecord()
    elif ((parm[1] == 'process') or (parm[1] == bytearray(b'process'))):
        print("Verb: Process Register")
        bots.processRecord()
    elif ((parm[1] == 'listidentifiers') or (parm[1] == bytearray(b'listidentifiers'))):
        print("Verb: Listidentifiers")
        bots.Listidentifiers()
    elif(parm[1] == 'dbtest'):
        ID = brapci_base.dbtest()
    elif(parm[1] == 'status'):
        print("STATUS: OK")
else:
    print("Argumentos não informado, veja ROBOTi --help")
