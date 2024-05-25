from colorama import Fore
import os
import sys
import mod_words

def version():
    return "v0.24.05.24"

def auto():
    print("AUTO")
    mod_words.words()

def run(parm):
    act = parm[1]
    print(Fore.BLUE+"Function: ",act)
    print(Fore.WHITE)

########################################### Início
print("ROBOTi-WORD",version())
print("===============================================")
diretorio = '/data/Brapci3.1/bots/WORD/'
os.chdir(diretorio)

if (len(sys.argv) > 1):
    parm = sys.argv
    run(parm)
else:
    auto()