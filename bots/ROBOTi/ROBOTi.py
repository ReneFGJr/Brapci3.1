# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-22
# @Title: Robos da Brapci (ROBOTi)

import sys
import roboti_help
import roboti_task
from colorama import Fore

def version():
    return "v0.23.12.22"

def run(parm):
    act = parm[1]
    print(Fore.BLUE+"Function: ",act)

    #************************************************* Functions
    #********************** ListIdentiers
    if (act == '1'):
        reg = roboti_task.nextHarvesting()



    print(Fore.WHITE)


########################################### Início
print("ROBOTi",version())
print("===============================================")

if (len(sys.argv) > 1):
    parm = sys.argv
    run(parm)
else:
    roboti_help.help()
