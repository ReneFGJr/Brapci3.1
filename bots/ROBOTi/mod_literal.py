import database
import mod_logs
import mod_nbr
import chardet

def check_end_dot():
    print("156 - Tratamento de assuntos com caracteres especiais")
    prop = 67
    qr = f"select id_n, n_name from brapci_rdf.rdf_data "
    qr += " inner join brapci_rdf.rdf_concept On d_r2 = id_cc"
    qr += " inner join brapci_rdf.rdf_literal on id_n = cc_pref_term"
    qr += f" where d_p = {prop} and n_name <> '' "
    qr += "group by id_n, n_name "
    qr += "order by n_name "

    row = database.query(qr)
    for item in row:
        id_n = item[0]
        title = item[1]
        titleO = item[1]
        title = title.replace('–','-')
        title = title.replace('"','')
        title = title.replace('“','')
        title = title.replace('”','')
        title = title.replace('- ','-')
        title = title.replace(' -','-')
        title = title.replace('<sup>*</sup>','')
        if title[-1] == '.':
            title = title[:-1]
        title = title.strip()
        title = mod_nbr.nbr_title(title)
        if (title != titleO):
            qu = f"update brapci_rdf.rdf_literal set n_name = '{title}' where id_n = {id_n}"
            print('=SUBJECT=>',title, id_n)
            database.update(qu)

def check_utf8():
    qr = "select id_n, n_name "
    qr += " from brapci_rdf.rdf_literal "
    qr += " where n_delete = 0"

    row = database.query(qr)
    # Verificar cada registro individualmente
    for id, dados in row:
        dados2 = dados
        try:
            # Tenta decodificar assumindo UTF-8. Note que isso requer que os dados sejam bytes.
            if dados is not None:
                #dados = dados.encode('utf-8')
                dados = dados.decode('utf-8')
                if (dados != dados2):
                    print("====================== UTF8")
                    print(dados)
                    print(dados2)
                    quit()

        except UnicodeDecodeError:
            # Relata o registro com problemas de decodificação
            print(f"Registro com ID {id} contém dados com erro de codificação.")


def check_duplicate():
    qr = "select * from "
    qr += "(select n_name, n_lang, count(*) as total, max(id_n), min(id_n) from brapci_rdf.rdf_literal where n_delete = 0 group by n_name, n_lang) as tabela "
    qr += "where (total > 1)"

    row = database.query(qr)
    for ln in row:
        ID1 = ln[3]
        ID2 = ln[4]
        qu = f"update brapci_rdf.rdf_concept set cc_pref_term = {ID1} where cc_pref_term = {ID2}"
        database.update(qu)
        qu = f"update brapci_rdf.rdf_data set d_literal = {ID1} where d_literal = {ID2}"
        database.update(qu)
        qu = f"update brapci_rdf.rdf_literal set n_delete = 1 , n_name = concat('[DELETED]',n_name) where id_n = {ID2}"
        database.update(qu)
        print(ln[0])


def check_double_name():
    qr = f"select id_n,n_name, length(n_name)"
    qr += "from brapci_rdf.rdf_literal "
    qr += "where (n_name <> '')"
    #qr += "where (n_name like '%Ana Maria Miel%')"
    row = database.query(qr)
    for ln in row:
        name = ln[1]
        sz = len(name)//2
        name1 = name[:sz]
        name2 = name[sz:]
        sz+=1
        name3 = name[sz:]
        name1 = name1[0:15]
        name2 = name2[0:15]
        name3 = name3[0:15]
        if (name1 == name2) or (name1 == name3):
            if (sz > 10):
                id = ln[0]
                qru = f"update brapci_rdf.rdf_literal set n_name = '{name1}' where id_n = {id}"
                database.update(qru)
                print(name1)
                print(name2)
                print(name3)
                print(sz)
                print("=====================")

def check_title():
    prop = 30
    qr = f"select id_n, n_name from brapci_rdf.rdf_data "
    qr += " inner join brapci_rdf.rdf_literal on id_n = d_literal"
    qr += " where (n_lang = 'nn')"
    qr += f" and d_p = {prop} "
    row = database.query(qr)
    for item in row:
        id_n = item[0]
        title = item[1]
        title = title.replace('"','')
        title = title.replace('“','')
        title = title.replace('”','')
        title = title.strip()
        title = mod_nbr.nbr_title(title)
        qu = f"update brapci_rdf.rdf_literal set n_name = '{title}', n_lang = 'pt' where id_n = {id_n}"
        print('==>',title)
        database.update(qu)

def check_trim():
    qr = f"select id_n,n_name from brapci_rdf.rdf_literal where (n_name like ' %') or (n_name like '%  %') or (n_name like '% :%')  or (n_name like '%::%')"
    qr += " or (n_name like '% .%')"
    row = database.query(qr)
    dd=0
    for ln in row:
        name = ln[1]
        name = name.strip().capitalize()
        name = name.replace('  ',' ')
        name = name.replace(' :',':')
        name = name.replace(' .','.')
        name = name.replace('::',':')
        id = ln[0]
        qru = f"update brapci_rdf.rdf_literal set n_name = '{name}' where id_n = {id}"
        database.update(qru)
        dd = dd + 1
        print('==>',name)
    qd = "COMMIT"
    database.update(qd)
    mod_logs.log('TASK_100',dd)

def check_all():
    qr = f"select id_n,n_name from brapci_rdf.rdf_literal"
    row = database.query(qr)
    for ln in row:
        name = ln[1]
        name2 = name.replace('"','')
        name2 = name2.replace("'",'')
        name2 = name2.replace("&amp.",'&')
        save = False
        try:
            n = name2[0]
            if (n > chr(126)):
                name2 = name2.strip().capitalize()
                if name2 != name:
                    save = True
                    print(name2)

            if (name != name2):
                save = True
                print("1="+name)
                print("2="+name2)
                print("============")
        except:
            print(f"skip erro - {name} - {name2}")
        if save == True:
            #name = name.strip().capitalize()
            id = ln[0]
            qru = f"update brapci_rdf.rdf_literal set n_name = '{name2}' where id_n = {id}"
            database.update(qru)

        #print(name)
    qd = "COMMIT"
    database.update(qd)


def register(term,lang):
    qr = f"select * from brapci_rdf.rdf_literal where (n_name = '{term}') and (n_lang = '{lang}')"
    row = database.query(qr)
    if row == []:
        qri = "insert into brapci_rdf.rdf_literal "
        qri += '(n_name, n_lock, n_lang)'
        qri += ' values '
        qri += f"('{term}',1,'{lang}')"
        row = database.query(qri)
        row = database.query(qr)
    rsp = row[0][0]

    qd = "COMMIT"
    database.update(qd)

    if (rsp == 0):
        print("OPS Register Name")
        quit()
    return rsp
