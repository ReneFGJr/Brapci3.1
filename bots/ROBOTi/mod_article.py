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

def process(rg):
    ID = rg[0]
    JNL = rg[6]
    IDA = rg[1]

    print(Fore.YELLOW+f"... Processando ISSUE ({ID}): "+Fore.GREEN+rg[1]+Fore.WHITE)

    path = mod_listidentify.directory(rg[0])+'.getRecord.json'

    try:
        ##print(path)
        f = open(path)
        data = json.load(f)
        f.close()

        #Verifica se existe o ID = METHODO 01
        IDX = check_method01(IDA,JNL)
        if IDX > 0:
            mod_listidentify.updateStatus(ID,8)
            return ""

        print("Method 02")
        IDX = check_method02(rg,IDA,JNL)

        print("================== NAO EXISTE")

        quit()


    except Exception as e:
        #mod_listidentify.updateStatus(ID,1)
        print("ERRO",e)

def check_method01(id,jnl):
    jnl = str(jnl)
    while (len(jnl) < 5):
        jnl = "0"+jnl

    #Monta ID do trabalho com o ID do Journal
    ID = id + "#"+jnl

    qr = "select id_cc from brapci_rdf.rdf_literal "
    qr += f"inner join brapci_rdf.rdf_data ON d_literal = id_n "
    qr += f"inner join brapci_rdf.rdf_concept ON d_r1 = id_cc "
    qr += f"where n_name = '{ID}' or n_name = '{id}'"
    qr += "group by id_cc"
    row = database.query(qr)

    if row == []:
        return 0

    if (len(row) == 1):
        return row[0][0]

    print("MAIS DE UM IDC")
    print(row)

    print(ID)
    quit()

def check_method02(data,jnl,id):

    title = []
    data = []

    for i in range(len(data)):
        keys = data[i].keys()
        for k in keys:
            ##print(f'RSP: {k}')
            if (k == 'title'):
                ##print("HELLO",k,i)
                title = data[i][k]

    for i in range(len(title)):
        print('==>',title[i])

        if '@' in title[i]:
            title[i] = title[i][:-3]

    ################################### Verifica se não existe cadastrado
    print(title)

    ## Method 01 - ID

    ## Phase I
    for i in range(len(title)):
        tit = title[i]
        qr = "select * from brapci_elatic.dataset "
        qr += f"where TITLE = '{tit}' "
        qr += f"and "
        print(qr)
        row = database.query(qr)
        print(row)

    quit()

    ## Phase I - Check Name
    for i in range(len(title)):
        tit = title[i]
        qr = "select * from brapci_elatic.dataset "
        qr += f"where TITLE = '{tit}' "
        print(qr)
        row = database.query(qr)
        print(row)
    quit()
