import database
import oaipmh_request
import mod_setSpec
import oaipmh_ListIdentifiers

from colorama import Fore

def harvesting():


    qr = "select is_url_oai, is_oai_token, is_source, id_is "
    qr += "from brapci.source_issue "
    qr += "where is_status = 0 limit 1"
    row = database.query(qr)

    if not row == []:
        print(row)

        # GET SETSPEC
        URL = str(row[0][0])
        JNL= str(row[0][2])
        print("URL=>",URL)
        XML = getSetSpec(URL,JNL)
        print('Status:',XML['status'])

        xml = oaipmh_ListIdentifiers.getURL(URL)

        # Phase IV - Check and Process XML File
        if (xml['status'] == '200'):
            # Phase IVa - Get setSpecs
            setSpec = oaipmh_ListIdentifiers.xml_setSpec(xml)
            # Phase IVb - Registers setSpecs
            reg = [[{JNL}]]
            print(reg[0][0])
            setSpec = mod_setSpec.process(setSpec,reg)
            # Phase IVc - Identifica Identify
            identifies = oaipmh_ListIdentifiers.xml_identifies(xml,setSpec,JNL)
            # Pahse IVd - Registra Identify
            mod_listidentify.registers(identifies,jnl)

        #Phase V - Token
        if (xml['status'] == '200'):
            #token = mod_source.token(xml)
            #mod_source.update(jnl,'100',token)
            if token == '':
                print(Fore.GREEN+"Fim da coleta"+Fore.WHITE)
                loop = 0
            else:
                print(Fore.YELLOW+"... Reprocessamento da Coleta "+Fore.GREEN+token+Fore.WHITE)
                loop = 1
            return loop
        else:
            #mod_source.update(jnl,xml['status'],'')
            print("FIM")

def getSetSpec(url,JNL):
    LINK = url + '?verb=ListSets'
    xml = oaipmh_request.get(LINK)

    if (xml['status'] == '200'):
        oaipmh_ListIdentifiers.xml_setSpecList(xml,JNL)


    return xml