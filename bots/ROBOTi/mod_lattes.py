import os
import database
from colorama import Fore

def import_file(file,hidden=''):
    if (os.path.isfile(file) == False):
        print("Arquivo "+file+" não foi localizado")
    else:
        print("Processando arquivo "+file)
        f = open(file,'r')
        ln = f.readlines()
        f.close()
        count = 0
        countMark = 0

        for l in ln:
            lt = l[0:16]
            kt = l[17:27]

            if (kt[0:1] == 'K'):
                count += 1
                countMark += 1

                if hidden != '':
                        qi = f"insert into brapci_lattes.k_to_n "
                        qi += f"(kn_idk, kn_idn, kn_status)"
                        qi += " values "
                        qi += f"('{kt}','{lt}',2)"
                        database.insert(qi)
                        if (countMark > 1000):
                            print(".",end="")
                            countMark = 0;
                else:
                    qr = f"select * from brapci_lattes.k_to_n where kn_idk  = '{kt}'"
                    row = database.query(qr)
                    if (row == []):
                        qi = f"insert into brapci_lattes.k_to_n "
                        qi += f"(kn_idk, kn_idn, kn_status)"
                        qi += " values "
                        qi += f"('{kt}','{lt}',2)"
                        database.insert(qi)
                        print(Fore.GREEN+f"Inserido {kt}"+Fore.WHITE)
                    else:
                        print(Fore.YELLOW+f"Já registrado {kt}"+Fore.WHITE)
