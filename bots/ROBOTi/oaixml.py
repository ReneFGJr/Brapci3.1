import json
import xmltodict

def convertXMLtoJSON(file,ID):
    import language
    import nbr
    import issue
    import type_id
    import license
    import brapci_base

    DT = brapci_base.getID(ID)
    sect = DT[0]
    journal = DT[1]

    f = open(file, "r")
    docXML = f.read()
    f.close()

    try:
        doc = xmltodict.parse(docXML)
    except:
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
        dc_section = dict(section=sect)
        dc_journal = dict(id_jnl=journal)


        ############################################# Título
        try:
            TIT = RCN['dc:title']
            if type(TIT) is list:

                for reg in TIT:
                    titulo = reg['#text']+'@'+language.check(reg['@xml:lang'])
                    dc_titulo.append(titulo)
            else:
                titulo = TIT['#text']+'@'+language.check(TIT['@xml:lang'])
                dc_titulo.append(titulo)
        except Exception as e:
            print("Erro a processar o Título",e)

        ############################################# Abstract
        try:
            TIT = RCN['dc:description']
            if type(TIT) is list:
                for reg in TIT:
                    titulo = reg['#text']+'@'+language.check(reg['@xml:lang'])
                    dc_abstract.append(titulo)
            else:
                titulo = TIT['#text']+'@'+language.check(TIT['@xml:lang'])
                dc_abstract.append(titulo)
        except Exception as e:
            print("Erro a processar o Resumo",e)

        ############################################# Author
        try:
            TIT = RCN['dc:creator']
            if type(TIT) is list:
                for reg in TIT:
                    dc_author.append(nbr.nbr_author(reg))
            else:
                reg = TIT
                dc_author.append(nbr.nbr_author(reg))
        except Exception as e:
            print("Erro a processar o Author",e)

        ############################################# Assuntos
        try:
            TIT = RCN['dc:subject']
            if type(TIT) is list:
                for reg in TIT:
                    titulo = nbr.nbr_subject(reg['#text'])+'@'+language.check(reg['@xml:lang'])
                    dc_subject.append(titulo)
            else:
                titulo = nbr.nbr_subject(TIT['#text'])+'@'+language.check(TIT['@xml:lang'])
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
                    lc = license.tipo(titulo)
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
                    reg = type_id.recognizer(reg)
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
                    reg = type_id.recognizer(reg)
                    if (reg['type'] == 'DOI'):
                        dc_doi.append(reg)
                    if (reg['type'] == 'HTTP'):
                        dc_link.append(reg)
            if type(TIT) is str:
                    reg = type_id.recognizer(TIT)
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
                    lg = language.check(reg['@xml:lang'])
                    source = issue.decode(reg['#text'],lg,source)
                    dc_source = source
            else:
                    lg = language.check(TIT['@xml:lang'])
                    source = issue.decode(TIT['#text'],lg,source)
                    dc_source = source
        except Exception as e:
            print("Erro a processar o Source - ",e)

        ############################################# Language
        try:
            TIT = RCN['dc:language']
            if type(TIT) is list:
                for reg in TIT:
                    dc_language.append(language.check(reg))
            else:
                reg = TIT
                dc_language.append(language.check(reg))
        except Exception as e:
            print("Erro a processar o Author",e)


    except Exception as e:
        print("Erro no XML")
        print(e)

    try:
        DC = [{'journal':dc_journal},'section':dc_section},{'title':dc_titulo},{'abstract':dc_abstract},{'author':dc_author},{'subject':dc_subject},{'source':dc_source},{'datePub':dc_datePub},{'DOI':dc_doi},{'http':dc_link},{'language':dc_language},{'license':dc_license}]
    except Exception as e:
        print("ERRO NO DC",e)

    print("SAVE=",file)
    file = file.replace('.xml','.json')
    f = open(file,'w')
    f.write(json.dumps(DC))
    f.close()