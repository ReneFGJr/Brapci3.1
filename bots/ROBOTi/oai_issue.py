# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-24
# @Title: oai_listidentify

import requests
import xml.etree.ElementTree as ET
import database
import mod_setSpec

table = 'brapci_oaipmh.oai_listidentify'

def register(ID,JNL):
    print("HELLO")

def getSetSpec(URL,JNL):
    # URL do XML
    url = URL + '?verb=ListSets'

    # Fazendo a requisição HTTP
    response = requests.get(url, verify=False)
    response.raise_for_status()  # Levanta um erro se a requisição falhar

    # Analisando o XML
    root = ET.fromstring(response.content)

    for set_element in root.findall('.//{http://www.openarchives.org/OAI/2.0/}set'):
        setSpec = set_element.find('{http://www.openarchives.org/OAI/2.0/}setSpec').text
        setName = set_element.find('{http://www.openarchives.org/OAI/2.0/}setName').text
        print(setSpec,setName)

        mod_setSpec.register(setSpec,JNL,setName)
