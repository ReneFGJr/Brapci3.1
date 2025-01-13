import mod_class
import database
import mod_data
import json
import requests
import sys
import oai_listidentify

def check_pbci():
    d = 0
    qr = "select * from brapci_elastic.dataset "
    qr += "where "
    qr += " JOURNAL = 12 "
    qr += " and `use` = 0 "
    row = database.query(qr)
    for ln in row:
        ID = ln[1]
        abs = ln[4] # JSON Field
        abs = json.loads(abs)
        try:
            txt = abs['Abstract']['pt'][0]
            if 'Acesso à revista' in txt or 'Acesso &agrave; revista' in txt or 'Acesso ao texto completo' in txt or 'http' in txt or 'acesso ao resumo' in txt or 'acesso ao texto completo' in txt:
                qu = f"update brapci_elastic.dataset set `use` = -99 where ID = {ID}"
                print(qu)
                database.update(qu)
        except:
            d=d+1
            #print("ABSTRACT NORMAL PBCI",ID)



def check_type():
    print("Checando tipo de publicação e o trabalho")
    qr = "select ID, CLASS, jnl_name "
    qr += " from brapci_elastic.dataset "
    qr += " inner join brapci.source_source on JOURNAL = id_jnl "
    qr += " where CLASS <> 'Proceeding' "
    qr += " and jnl_collection = 'EV' "

    row = database.query(qr)

    classN = mod_class.getClass('Proceeding')
    for item in row:
        print(item)
        ID = item[0]

        qu = f"update brapci_rdf.rdf_concept set cc_class = {classN} where id_cc = {ID} "
        database.update(qu)

def remove_duplicate():
    qr = "select ID, `use` from brapci_elastic.dataset "
    qr += " where `use` > 0 and JOURNAL = 75 "
    row = database.query(qr)

    for item in row:
        url = 'https://cip.brapci.inf.br/api/rdf/deleteConcept/$ID?token=ff63a314d1ddd425517550f446e4175e'
        # Fazendo a chamada de API GET
        url = url.replace('$ID',str(item[0]))
        response = requests.get(url)

        # Verificando se a chamada foi bem sucedida
        if response.status_code == 200:
            # Convertendo a resposta de JSON para um dicionário Python
            data = response.json()
            print(data)
        else:
            print("Falha na chamada da API: ", response.status_code)

def check_double_issue():
    prop = mod_class.getClass('isPartOfSource')
    qr = "select * from ("
    qr += "SELECT d_r2, d_p, count(*) as total, max(id_d) as id_d FROM brapci_rdf.rdf_data "
    qr += f" WHERE d_p = {prop} "
    qr += " group by d_r2,d_p "
    qr += ") as tabela where total > 1 order by d_r2, total desc "
    qr += " limit 1"

    row = database.query(qr)
    for line in row:
        ID = line[0]
        print("Processando ",ID,"========================")
        qr = "select * from brapci_oaipmh.oai_listidentify where oai_rdf = " + str(ID)
        row2 = database.query(qr)
        ln = 0
        for line2 in row2:
            IDd = line2[0]
            oai_listidentify.chageStatusID(IDd,1)
            if (ln > 0):
                oai_listidentify.zeraRDF(IDd)
        mod_data.delete_data(ID)
        sys.exit()

    print("Total de ",len(row))

def check_duplicate():
    qr = "select JOURNAL, TITLE, AUTHORS, ID, YEAR from brapci_elastic.dataset "
    qr += " where `use` = 0 "
    qr += "order by JOURNAL, TITLE, AUTHORS, YEAR, PDF desc, ID "
    row = database.query(qr)

    last = ''
    lastID = ''
    tot = 0

    for item in row:
        name = str(item[0]) + ' | ' + item[1]+' | ' + item[2]+' | ' + str(item[4])
        ID = item[3]

        if (name == last):
            print(ID,lastID,name)
            tot = tot + 1
            qu = "update brapci_elastic.dataset "
            qu += f" set `use` = {lastID} "
            qu += f" where ID = {ID} "
            print("===>",qu)
            database.update(qu)

        last = name
        lastID = ID
    print("Total",tot)
    remove_duplicate()