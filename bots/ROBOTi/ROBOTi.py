import sys
import env
import brapci_base

if (len(sys.argv) > 1):
    parm = sys.argv
    if (parm[1] == '--help'):
        brapci_base.help_roboti()
    elif(parm[1] == 'run'):
        ####################################### NEXT
        act = brapci_base.next_action()

        ####################################### GET
        if ((act == 'oai_identifty') or (act == bytearray(b'oai_identifty'))):
            import metabot
            metabot.getNextIdentify()
    elif(parm[1] == 'tst'):
        ID = lib_oai_brapci.test()
else:
    print("Argumentos não informado, veja ROBOTi --help")
