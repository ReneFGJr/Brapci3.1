import re
import json
import string
import array
from colorama import Fore
import mod_listidentify
import mod_literal
import mod_concept
import mod_class
import database
import traceback
import sys

def identify(rg):
    ID = rg[0]
    JNL = rg[6]
    ISSUE = rg[10]

    try:
        path = mod_listidentify.directory(ID)+'.getRecord.json'

        print("PATH: "+path)

        f = open(path)
        data = json.load(f)
        f.close()

        source = []

        print(data)

        if (ISSUE == 0) or (ISSUE == None):
            for i in range(len(data)):
                keys = data[i].keys()
                for k in keys:
                    ##print(f'RSP: {k}')
                    if (k == 'source'):
                        ##print("HELLO",k,i)
                        source = data[i][k]

            vol = formatVol(source['vol'])
            nr = formatNr(source['nr'])
            year = source['year']
            if year == '':
                year = 9999

            print(f"Year:{year}\nVol:{vol},\nNr.{nr}")
            sys.exit()

            qr = 'select * from brapci.source_issue '
            qr += 'where '
            qr += 'is_source = '+str(JNL)
            qr += ' AND is_year = '+str(year)
            qr += ' AND is_vol = \''+vol+'\''
            qr += ' AND is_nr = \''+nr+'\''
            row = database.query(qr)

            if (row == []):
                row = create_issue(JNL,year,vol,nr)
        else:
            print("ISSUE JÀ EXISTE")

            qr = 'select * from brapci.source_issue '
            qr += 'where '
            qr += 'is_source_issue = '+str(ISSUE)
            row = database.query(qr)


    except Exception as e:
        print("Erro ISSUE",e)
        row = []

    return row

def create_issue_rdf(JNL,year,vol,nr):
    ID = str(JNL)
    while len(ID) < 5:
        ID = "0"+ID

    ID = "JNL:"+ID+":"+str(year)
    ID += '-'+vol

    lt = mod_literal.register(ID,'nn')
    cl = mod_class.getClass('Issue')
    Issue = mod_concept.register(cl,lt)

    return Issue

def create_issue(JNL,year,vol,nr):

    JNLs = 'ISSUE:JNL:'+str(JNL)
    while len(JNLs) < 5:
        JNLs = '0' + JNLs
    JNLs += ':'+str(year)
    JNLs += '-'+extract_numbers(vol)
    JNLs += '-'+extract_numbers(nr)

    lt = mod_literal.register(JNLs,'nn')
    cl = mod_class.getClass('Issue')
    Issue = mod_concept.register(cl,lt)

    qr = "select * from brapci.source_issue "
    qr += f"where is_source_issue = {Issue}"
    row = database.query(qr)

    if row == []:
        qri = "insert into brapci.source_issue "
        qri += "(is_source, is_year, is_vol, is_vol_roman, is_nr, is_thema, "
        qri += "is_source_issue, is_place, is_edition, is_cover, is_card,"
        qri += "is_url_oai)"
        qri += ' values '
        qri += f"({JNL},{year},'{vol}','','{nr}','', "
        qri += f"{Issue}, '', "
        qri += "'','','','')"
        database.insert(qri)
        row = database.query(qr)
    return row

def process(rg):
    ID = rg[0]

    print(Fore.YELLOW+f"... Processando ISSUE ARTICLE ({ID}): "+Fore.GREEN+rg[1]+Fore.WHITE)

    ######################### Identify ##
    try:
        row = identify(rg)
        ISSUE = row[0][3]
        mod_listidentify.updateIssue(ID,ISSUE)
        mod_listidentify.updateStatus(ID,7)
    except Exception as e:
        mod_listidentify.updateStatus(ID,1)
        print("ERROR #22",e)
        mensagem = traceback.format_exc()
        print("Ocorreu um erro:", mensagem)
        mod_listidentify.updateStatus(ID,1)

def extract_numbers(text):
    # Utilizando compreensão de lista e isdigit() para extrair números
    t = ''.join([char for char in text if char.isdigit()])
    if t == '':
        t = '0'
    return t

def formatVol(vol):
    vol = vol.replace("'","")
    if 'v.' in vol:
        vol = vol
    else:
        vol = 'v. '+vol
    vol = vol.replace("'","")
    if vol == 'v. ':
        vol = ''
    return vol

def formatNr(nr):
    nr = nr.replace("'","")
    nr = nr.replace('especial','esp')
    m = []
    m.append('jan')
    m.append('fev')
    m.append('mar')
    m.append('abr')
    m.append('maio')
    m.append('jun')
    m.append('jul')
    m.append('ago')
    m.append('set')
    m.append('out')
    m.append('nov')
    m.append('dez')
    for mes in m:
        nr = nr.replace(mes+'/','')
        nr = nr.replace(mes,'')

    if 'n.' in nr:
        nr = nr
    else:
        nr = 'n. '+nr

    if nr == 'n. ':
        nr = ''

    nr = nr.replace("'","")
    return nr

def decode(n,lg,vl):

    try:
        n = n.lower()
    except:
        print("Erro no Lower da Legemda")

    try:
        vol = vl['vol']
        nr =  vl['nr']
        year =  vl['year']
        theme =  vl['theme']
    except Exception as e:
        vol = ''
        nr = ''
        year = ''
        theme = ''
        print("ERRO de Processamento da legenda - ".e)

    ############################################## Recupera ANO
    ################### method 01 - (YEAR)
    try:
        yearR = re.findall('\(\d{4}\)',n)
        if yearR == []:
            yearR = re.findall('\ \d{4}\;',n)
        if yearR == []:
            yearR = re.findall('\ \d{4}',n)
        for y in yearR:
            year = y.replace('(','')
            year = year.replace(')','')
            year = year.replace(';','')
    except Exception as e:
        print("Erro ao processar o Ano",e)

    ############################################## Recupera VOLUME
    ################### method 01 - (vol. X)
    try:
        vols = ['vol. ','vol.','v. ','v.']
        vol = ''
        for cg in vols:
            volR = re.findall(cg+'\d',n)
            volR = re.findall(" "+cg+"[a-zA-Z0-9\/\.\-_\+]+",n)
            if (vol == '') and (volR != []):
                vol = volR[0]
                vol = vol.replace(cg,'').strip()
    except Exception as e:
        print("Erro ao processar o VOLUME",e)
    ############################################## Recupera NUMERO
    ################### method 01 - (vol. X)
    try:
        vols = ['nr. ','num.','n. ','n.','núm.', ' Esp', ' esp']
        nr = ''
        for cg in vols:
            volR = re.findall(" "+cg+"[a-zA-Z0-9\/\.\-_\+]+",n)
            #print("volRY",volR)
            if (nr == '') and (volR != []):
                nr = volR[0]
                nr = nr.replace(cg,'').strip()
    except Exception as e:
        print("Erro ao processar o NUMERO",e)

    ############################################## Finaliza
    try:
        dc = dict(vol=vol,nr=nr,year=year,theme=theme)
    except Exception as e:
        print("Problema ao montar retorno",e)

    print(dc)
    sys.exit()

    return dc