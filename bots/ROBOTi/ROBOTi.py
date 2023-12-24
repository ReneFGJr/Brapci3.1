# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-22
# @Title: Robos da Brapci (ROBOTi)

import sys
import roboti_help
import roboti_task
import roboti_clear
import mod_setSpec
import mod_listidentify
import oaipmh_ListIdentifiers
from colorama import Fore

def version():
    return "v0.23.12.22"

def run(parm):
    act = parm[1]
    print(Fore.BLUE+"Function: ",act)

    #************************************************* Functions
    #********************** ListIdentiers
    if (act == '1'):
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





        #oaipmh_setSpec.register('Zeb:ART',16)

    elif (act == 'clear'):
        roboti_clear.clear(0)



    print(Fore.WHITE)


########################################### Início
print("ROBOTi",version())
print("===============================================")

if (len(sys.argv) > 1):
    parm = sys.argv
    run(parm)
else:
    roboti_help.help()
