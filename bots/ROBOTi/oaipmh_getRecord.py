# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-24
# @Title: Modulo de GetRecord

from colorama import Fore
import oaipmh_request
import time
import mod_listidentify
import os.path
import xmltodict
import json

import mod_language
import mod_nbr
import mod_issue
import mod_type_id
import mod_license
import mod_source


def process(rg):
    ID = rg[0]
    print(Fore.YELLOW+f"... Processando ({ID}): "+Fore.GREEN+rg[1]+Fore.WHITE)
    path = mod_listidentify.directory(rg[0])+'.getRecord.xml'
    if not os.path.isfile(path):
        mod_listidentify.updateStatus(ID,1)
        print(Fore.RED,"... ERRO - File or found",path,Fore.WHITE)
    else:
        JNL = rg[6]
        sect = rg[4]

        f = open(path, "r")
        docXML = f.read()
        f.close()

        try:
            doc = xmltodict.parse(docXML)
        except:
            mod_listidentify.updateStatus(ID,1)
            print("Erro ao converter o XML - Identify")
            return False

        try:
            OAIPMH = doc['OAI-PMH']
            RCN = OAIPMH['GetRecord']['record']['metadata']['oai_dc:dc']

            DC = []
            dc_titulo = []
            dc_abstract = []
            dc_author = []
            dc_subject = []
            dc_source = []
            dc_datePub = []
            dc_doi = []
            dc_link = []
            dc_language = []
            dc_license = []
            dc_section = {"section":sect}
            dc_journal = {"id_jnl":JNL}

            ############################################# Título
            try:
                TIT = RCN['dc:title']
                if type(TIT) is list:

                    for reg in TIT:
                        titulo = reg['#text']+'@'+mod_language.check(reg['@xml:lang'])
                        dc_titulo.append(titulo)
                else:
                    titulo = TIT['#text']+'@'+mod_language.check(TIT['@xml:lang'])
                    dc_titulo.append(titulo)
            except Exception as e:
                print("Erro a processar o Título",e)

            ############################################# Abstract
            try:
                TIT = RCN['dc:description']
                if type(TIT) is list:
                    for reg in TIT:
                        titulo = reg['#text']+'@'+mod_language.check(reg['@xml:lang'])
                        dc_abstract.append(titulo)
                else:
                    titulo = TIT['#text']+'@'+mod_language.check(TIT['@xml:lang'])
                    dc_abstract.append(titulo)
            except Exception as e:
                print("Erro a processar o Resumo",e)

            ############################################# Author
            try:
                TIT = RCN['dc:creator']
                if type(TIT) is list:
                    for reg in TIT:
                        dc_author.append(mod_nbr.nbr_author(reg))
                else:
                    reg = TIT
                    dc_author.append(mod_nbr.nbr_author(reg))
            except Exception as e:
                print("Erro a processar o Author",e)

            ############################################# Assuntos
            try:
                TIT = RCN['dc:subject']
                if type(TIT) is list:
                    for reg in TIT:
                        titulo = mod_nbr.nbr_subject(reg['#text'])+'@'+mod_language.check(reg['@xml:lang'])
                        dc_subject.append(titulo)
                else:
                    titulo = mod_nbr.nbr_subject(TIT['#text'])+'@'+mod_language.check(TIT['@xml:lang'])
                    dc_subject.append(titulo)
            except Exception as e:
                print("Erro a processar o Assuntos",e)

            ############################################# Date
            try:
                TIT = RCN['dc:date']
                if type(TIT) is list:
                    for reg in TIT:
                        titulo = reg['#text']
                        print("TT=>",titulo)
                        dc_datePub.append(titulo)
                else:
                    titulo = TIT
                    dc_datePub.append(titulo)
            except Exception as e:
                print("Erro a processar o Data de publicação",e)

            ############################################# License
            try:
                TIT = RCN['dc:rights']

                if type(TIT) is list:
                    for reg in TIT:
                        titulo = reg['#text']
                        lc = mod_license.tipo(titulo)
                        dc_license.append(lc)
                else:
                    titulo = TIT
                    dc_license.append(titulo)
            except Exception as e:
                print("Erro a processar o Licenca",e)

            ############################################# identifier
            try:
                TIT = RCN['dc:identifier']
                if type(TIT) is list:
                    for reg in TIT:
                        reg = mod_type_id.recognizer(reg)
                        if (reg['type'] == 'DOI'):
                            dc_doi.append(reg)
                        if (reg['type'] == 'HTTP'):
                            dc_link.append(reg)
            except Exception as e:
                print("Erro a processar o Identifier",e)


            #relation
            try:
                TIT = RCN['dc:relation']
                if type(TIT) is list:
                    for reg in TIT:
                        reg = mod_type_id.recognizer(reg)
                        if (reg['type'] == 'DOI'):
                            dc_doi.append(reg)
                        if (reg['type'] == 'HTTP'):
                            dc_link.append(reg)
                if type(TIT) is str:
                        reg = mod_type_id.recognizer(TIT)
                        if (reg['type'] == 'DOI'):
                            dc_doi.append(reg)
                        if (reg['type'] == 'HTTP'):
                            dc_link.append(reg)

            except Exception as e:
                print("Erro a processar o Identifier",e)


            ############################################# Source
            try:
                source = dict(vol='',nr='',year='',theme='')
                TIT = RCN['dc:source']
                if type(TIT) is list:
                    for reg in TIT:
                        lg = mod_language.check(reg['@xml:lang'])
                        source = mod_issue.decode(reg['#text'],lg,source)
                        dc_source = source
                else:
                        lg = mod_language.check(TIT['@xml:lang'])
                        source = mod_issue.decode(TIT['#text'],lg,source)
                        dc_source = source
            except Exception as e:
                print("Erro a processar o Source - ",e)

            ############################################# Language
            try:
                TIT = RCN['dc:language']
                if type(TIT) is list:
                    for reg in TIT:
                        dc_language.append(mod_language.check(reg))
                else:
                    reg = TIT
                    dc_language.append(mod_language.check(reg))
            except Exception as e:
                print("Erro a processar o Author",e)
        except Exception as e:
            print(Fore.RED,"Erro no XML",Fore.WHITE)
            print(e)
        try:
            DC = [{'journal':dc_journal},{'section':dc_section},{'title':dc_titulo},{'abstract':dc_abstract},{'author':dc_author},{'subject':dc_subject},{'source':dc_source},{'datePub':dc_datePub},{'DOI':dc_doi},{'http':dc_link},{'language':dc_language},{'license':dc_license}]
        except Exception as e:
            print(Fore.RED,"ERRO NO DC",Fore.WHITE,e)

        file = path.replace('.xml','.json')


        try:
            f = open(file,'w')
            f.write(json.dumps(DC))
            f.close()
        except Exception as e:
            mod_listidentify.updateStatus(ID,1)
            print("Erro",e)



        mod_listidentify.updateStatus(ID,6)

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
        time.sleep(0.1)
    return True
