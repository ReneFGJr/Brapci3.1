# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-24
# @Title: oai_listidentify

import requests
import xml.etree.ElementTree as ET
import database
import mod_setSpec
import mod_listidentify
from colorama import Fore

table = 'brapci_oaipmh.oai_listidentify'

def register(ID,JNL):
    print("HELLO")

def updateIssues():
    print("Updating issues...")
    QU = """
        UPDATE brapci.source_issue si
        LEFT JOIN (
            SELECT
                d1.d_r1,
                COUNT(*) AS total
            FROM `rdf_data` as d1
            INNER JOIN brapci_rdf.rdf_concept as c1 ON d_r1 = c1.id_cc and c1.cc_class = 5 and c1.cc_status = 1
            INNER JOIN brapci_rdf.rdf_concept as c2 ON d_r2 = c2.id_cc and c2.cc_status = 1
            WHERE d_p = 31
            GROUP BY d1.d_r1
        ) AS t
            ON t.d_r1 = si.is_source_issue
        SET
            si.is_works = COALESCE(t.total, 0)
        WHERE
            si.is_works <> COALESCE(t.total, 0);
    """
    row = database.query(QU)

def getListIdentifiers(URL,JNL, token, ISSUE):

    # URL do XML
    if token != '':
        LINK = URL + '?verb=ListIdentifiers&resumptionToken=' + token
    else:
        LINK = URL + '?verb=ListIdentifiers&metadataPrefix=oai_dc'
    print(Fore.YELLOW+"... Recuperando: "+Fore.GREEN+f"{LINK}"+Fore.WHITE)

    # Fazendo a requisição HTTP
    XML = '400'

    try:
        response = requests.get(LINK, verify=False)
        response.raise_for_status()  # Levanta um erro se a requisição falhar

        # Analisando o XML
        root = ET.fromstring(response.content)

        for set_element in root.findall('.//{http://www.openarchives.org/OAI/2.0/}header'):
            identifier = set_element.find('{http://www.openarchives.org/OAI/2.0/}identifier').text
            datestamp = set_element.find('{http://www.openarchives.org/OAI/2.0/}datestamp').text
            setSpec = set_element.find('{http://www.openarchives.org/OAI/2.0/}setSpec').text
            deleted = set_element.get('status') == 'deleted'  # Verifica se o status é 'deleted'

            datestamp = datestamp.replace('T',' ')
            datestamp = datestamp.replace('Z','')

            mod_listidentify.register(identifier,JNL,setSpec,datestamp,deleted,ISSUE)
            print(identifier,datestamp,setSpec,deleted)
    except Exception as e:
        print("ERRO",e)

def getSetSpec(URL,JNL):
    # URL do XML
    url = URL + '?verb=ListSets'

    # Fazendo a requisição HTTP
    XML = '400'

    try:
        response = requests.get(url, verify=False)
        response.raise_for_status()  # Levanta um erro se a requisição falhar

        # Analisando o XML
        root = ET.fromstring(response.content)

        for set_element in root.findall('.//{http://www.openarchives.org/OAI/2.0/}set'):
            setSpec = set_element.find('{http://www.openarchives.org/OAI/2.0/}setSpec').text
            setName = set_element.find('{http://www.openarchives.org/OAI/2.0/}setName').text

            mod_setSpec.register(setSpec,JNL,setName)
        XML = '200'
    except:
        XML = '501'
    return XML
