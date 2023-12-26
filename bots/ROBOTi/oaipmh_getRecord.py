# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-24
# @Title: Modulo de GetRecord

from colorama import Fore
import oaipmh_request
import time
import mod_listidentify
import os.path

def get(rg):
    identify = rg[1]
    url = rg[2]
    ID = rg[0]

    LINK = url + '?verb=GetRecord&metadataPrefix=oai_dc&identifier='+identify
    print(Fore.YELLOW+"... Recuperando: "+Fore.GREEN+f"{LINK}"+Fore.WHITE)

    file = mod_listidentify.directory(ID)+'.getRecord.xml'
    print(Fore.YELLOW+"... Arquivo: "+Fore.GREEN+f"{file}"+Fore.WHITE)

    if os.path.exists(file):
        print("FILE EXISTE")
        mod_listidentify.updateStatus(ID,5)
    else:
        xml = oaipmh_request.get(LINK)
        if (xml['status'] == '200'):
            f = open(file,'w')
            f.write(xml['content'])
            f.close()
        mod_listidentify.updateStatus(ID,5)
        time.sleep(0.5)
    return True
