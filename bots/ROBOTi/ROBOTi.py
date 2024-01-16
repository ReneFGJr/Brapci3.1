# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-22
# @Title: Robos da Brapci (ROBOTi)

import sys
import roboti_help
import roboti_task
import roboti_clear
import mod_setSpec
import mod_listidentify
import mod_source
import mod_article
import oaipmh_ListIdentifiers
import oaipmh_getRecord
import mod_issue
from colorama import Fore

def version():
    return "v0.23.12.22"

def run(parm):
    act = parm[1]
    print(Fore.BLUE+"Function: ",act)

    #************************************************* Functions
    #********************** ListIdentiers - LOOP
    if (act == '1'):
        loop = 1
        while loop > 0:
            loop += 1
            lp = ListIdentiers()
            print("LOOP - ",loop)
            if (loop > 50) or (lp == 0):
                loop = 0
    #********************** ListIdentiers - LOOP
    if (act == '2'):
        loop = 1
        while loop > 0:
            loop += 1
            lp = GetRecord()
            print("LOOP - ",loop)
            if (loop > 5) or (lp == 0):
                loop = 0

    #********************** Preprocess - LOOP
    if (act == '3'):
        loop = 1
        while loop > 0:
            loop += 1
            lp = ProcessRecord()
            print("LOOP - ",loop)
            if (loop > 10) or (lp == 0):
                loop = 0

    #********************** CheckEdition - LOOP - 6
    if (act == '4'):
        loop = 1
        while loop > 0:
            loop += 1
            lp = ProcessRecordIssue()
            print("LOOP - ",loop)
            if (loop > 10) or (lp == 0):
                loop = 0

    #********************** Article - LOOP
    if (act == '5'):
        loop = 1
        end = 1
        while loop > 0:
            loop += 1
            lp = ProcessArticle()
            print("LOOP - ",loop)
            if (loop > end) or (lp == 0):
                loop = 0

    #********************** Clear
    elif (act == 'clear'):
        roboti_clear.clear(0)
    elif (act == 'testdb'):
        import bot_test_db
        bot_test_db.dbtest()

    print(Fore.WHITE)

def ProcessArticle():
    # Phase I - get Next Records
    reg = roboti_task.nextGetRecords(7)

    # Phase II - Processa arquivos
    if (reg != []):
        for it in reg:
            mod_article.process(it)

def ProcessRecordIssue():
    # Phase I - get Next Records
    reg = roboti_task.nextGetRecords(6)

    # Phase II - Processa arquivos
    if (reg != []):
        for it in reg:
            mod_issue.process(it)

def ProcessRecord():
    # Phase I - get Next Records
    reg = roboti_task.nextGetRecords(5)

    # Phase II - Processa arquivos
    if (reg != []):
        for it in reg:
            oaipmh_getRecord.process(it)

def GetRecord():
    # Phase I - get Next Records
    reg = roboti_task.nextGetRecords(1)

    # Phase II - Coleta arquivos
    if (reg != []):
        for it in reg:
            oaipmh_getRecord.get(it)

    # Phase III - Fim do processo
    print(Fore.GREEN+"... Fim do processamento"+Fore.WHITE)

def ListIdentiers():
    # Phase I
    reg = roboti_task.nextHarvesting()
    # Phase II - Valie
    if not (roboti_task.valid(reg)):
        return False

    xml = oaipmh_ListIdentifiers.getSetSpec(reg[0])
    if (xml['status'] == '200'):
        setSpec = oaipmh_ListIdentifiers.xml_setSpecList(xml,reg[0][0])

    # Phase III - GetList
    jnl = reg[0][0]
    xml = oaipmh_ListIdentifiers.get(reg[0])

    # Phase IV - Check and Process XML File
    if (xml['status'] == '200'):
        # Phase IVa - Get setSpecs
        setSpec = oaipmh_ListIdentifiers.xml_setSpec(xml)
        # Phase IVb - Registers setSpecs
        setSpec = mod_setSpec.process(setSpec,reg)
        # Phase IVc - Identifica Identify
        identifies = oaipmh_ListIdentifiers.xml_identifies(xml,setSpec,jnl)
        # Pahse IVd - Registra Identify
        mod_listidentify.registers(identifies,jnl)

    #Phase V - Token
    if (xml['status'] == '200'):
        token = mod_source.token(xml)
        mod_source.update(jnl,'100',token)
        if token == '':
            print(Fore.GREEN+"Fim da coleta"+Fore.WHITE)
            loop = 0
        else:
            print(Fore.YELLOW+"... Reprocessamento da Coleta "+Fore.GREEN+token+Fore.WHITE)
            loop = 1
        return loop
    else:
        mod_source.update(jnl,xml['status'],'')



########################################### Início
print("ROBOTi",version())
print("===============================================")

if (len(sys.argv) > 1):
    parm = sys.argv
    run(parm)
else:
    roboti_help.help()
