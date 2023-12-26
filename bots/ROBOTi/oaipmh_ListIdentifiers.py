# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-24
# @Title: Modulo de ListIdentifiers

from colorama import Fore
import oaipmh_request
import xmltodict
import mod_listidentify

def get(jnl):
    token = jnl[4]
    url = jnl[1]

    if token != '':
        LINK = url + '?verb=ListIdentifiers&resumptionToken=' + token
    else:
        LINK = url + '?verb=ListIdentifiers&metadataPrefix=oai_dc'
    print(Fore.YELLOW+"... Recuperando: "+Fore.GREEN+f"{LINK}"+Fore.WHITE)

    xml = oaipmh_request.get(LINK)

    return xml

def xml_setSpec(xml):
    xml = xml['content']
    setSpec = {}
    try:
        doc = xmltodict.parse(xml)
        doc = doc['OAI-PMH']
        doc = doc['ListIdentifiers']

        for xdoc in doc['header']:
            if type(xdoc['setSpec']) is list:
                spec = xdoc['setSpec'][0]
            else:
                spec = xdoc['setSpec']
            if not spec in setSpec:
                setSpec[spec] = 0
        return {'status':True,'setSpec':setSpec}

    except Exception as e:
        print("Erro ao Abrir o XML",e)
        return {'status':False,'setSpec':setSpec}

def xml_identifies(xml,setSpec):
    xml = xml['content']
    identifiers = {}

    try:
        doc = xmltodict.parse(xml)
    except Exception as e:
        print("Erro no XML",e)
        quit()

    doc = doc['OAI-PMH']
    doc = doc['ListIdentifiers']

    for xdoc in doc['header']:
        id = xdoc['identifier']
        date = xdoc['datestamp']

        ############################ Registro deletado
        deleted = 0
        try:
            deleted = xdoc['@status']
            if (deleted == 'deleted'):
                deleted = 1
        except Exception as e:
            deleted = 0

        ############################ setSepc
        if type(xdoc['setSpec']) is list:
            spec = xdoc['setSpec'][0]
        else:
            spec = xdoc['setSpec']

        ############################ Register
        identifiers[id] = {'setSpec':setSpec[spec],'date':date,'deleted':deleted}
    return identifiers
