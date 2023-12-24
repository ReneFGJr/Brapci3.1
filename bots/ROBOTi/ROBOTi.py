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
import oaipmh_ListIdentifiers
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
            print("L O O P - ",loop,lp)
            if (loop > 50) or (lp == 0):
                loop = 0

    elif (act == 'clear'):
        roboti_clear.clear(0)
    elif (act == 'testdb'):
        import bot_test_db
        bot_test_db.dbtest()

    print(Fore.WHITE)

def ListIdentiers ():
        # Phase I
        reg = roboti_task.nextHarvesting()
        # Phase II - Valie
        if not (roboti_task.valid(reg)):
            return False
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
            identifies = oaipmh_ListIdentifiers.xml_identifies(xml,setSpec)
            # Pahse IVd - Registra Identify
            mod_listidentify.registers(identifies,jnl)

        #Phase V - Token
        if (xml['status'] == '200'):
            token = mod_source.token(xml)
            mod_source.update(jnl,'200',token)
            if token == '':
                print(Fore.GREEN+"Fim da coleta"+Fore.WHITE)
                loop = 0
            else:
                print(Fore.YELLOW+"... Reprocessamento da Coleta "+Fore.GREEN+token+Fore.WHITE)
                loop = 1
            return loop

        if (xml['status'] != '200'):
            mod_source.update(jnl,xml['status'],'')



########################################### Início
print("ROBOTi",version())
print("===============================================")

if (len(sys.argv) > 1):
    parm = sys.argv
    run(parm)
else:
    roboti_help.help()
