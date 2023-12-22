import brapci_base

############################### Funcoes
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
    XROWd = metabot.getNextListIdentifier()
    XROW = XROWd[0]

    if XROW > 0:
        print("Processando lista - Publicação ",XROWd[3])

        if (XROW > 0):
            loop = 1
            while loop == 1:
                print("Recuperando dados do provedor")
                row = brapci_base.getListIdentifier(XROW)
                print("Dados do provedor URL:",row[0])
                url = str(row[0])
                token = str(row[1])

                if (url != ''):
                    print("Recuperando dados do provedor (Site)")

                    try:
                        if (url == None):
                            url = ''
                        xml = oaipmh.ListIdentifiers(url,token)
                        brapci_base.zeraToken(XROW)
                        if (xml != ''):
                            print("Processando lista de arquivos XML",len(XML))
                            loop = brapci_base.processListIdentifiers(XROW,xml)
                    except Exception as e:
                        loop = 0
                        print("Erro ao processar - ListIdentifiers (1)")
                        print(e)
                else:
                    loop = 0
                    print("Nada para processar - ListIdentifiers (2)")

################################# R U N
def run():
    qr = ""

    ####################################### NEXT
    act = brapci_base.next_actions()

    print("     Agendamentos ",len(act),' tarefas')

    for tasks in act:
        TASK = tasks[1]
        execute(TASK)

    if (len(act) == 0):
        TASK = 'none'
        execute(TASK)

def execute(act):
    ####################################### GET
    if (act == 'none'):
        print("Sem ações agendada")
    elif ((act == 'oai_identifty') or (act == bytearray(b'oai_identifty'))):
        print("====================================== Identify")
        Identify()
    elif ((act == 'l') or (act == 'oai_listidentifiers') or (act == bytearray(b'oai_listidentifiers')) or (act == bytearray(b'l'))):
        print("============================== List Identifiers")
        Listidentifiers()
    elif ((act == 'oai_getregister') or (act == bytearray(b'oai_getregister')) or (act == bytearray(b'g'))):
        print("===================================== GetRecord")
        GetRecord()
    elif ((act == 'process') or (act == bytearray(b'process'))):
        print("================================ Process Record")
        processRecord()
    elif ((act == 'translate') or (act == bytearray(b'translate'))):
        print("===================================== Translate")
        import translate
        translate.translateNext()
    else:
        print(f".ROBOTi - Action not recognized {act}")