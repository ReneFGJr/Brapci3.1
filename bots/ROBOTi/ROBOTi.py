import sys
import lib_oai_brapci

if (len(sys.argv) > 1):
    parm = sys.argv
    if (parm[1] == '--help'):
        lib_oai_brapci.help_roboti()
    elif(parm[1] == 'run'):
        act = lib_oai_brapci.next_action()
        print("Task: "+act)
        if (act == 'IDENT'):
            ID = lib_oai_brapci.getIDENTIFY()
else:
    print("Argumentos não informado, veja ROBOTi --help")
