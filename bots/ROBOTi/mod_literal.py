import database
import mod_logs
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
    qr = f"select * from brapci_rdf.rdf_data "
    qr += " brapci_rdf.rdf_literal on id_n = d_literal"
    qr += " where (n_lang = 'nn')"
    qr += f" d_p = {prop} "
    row = database.query(qr)
    for item in row:
        print(item)

def check_trim():
    qr = f"select id_n,n_name from brapci_rdf.rdf_literal where (n_name like ' %')"
    row = database.query(qr)
    dd=0
    for ln in row:
        name = ln[1]
        name = name.strip().capitalize()
        id = ln[0]
        qru = f"update brapci_rdf.rdf_literal set n_name = '{name}' where id_n = {id}"
        database.update(qru)
        dd = dd + 1
        print(name)
    mod_logs.log('TASK_100',dd)

def check_all():
    qr = f"select id_n,n_name from brapci_rdf.rdf_literal"
    row = database.query(qr)
    for ln in row:
        name = ln[1]
        name2 = name.replace('"','')
        name2 = name2.replace("'",'')
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
            print("skip")
        if save == True:
            #name = name.strip().capitalize()
            id = ln[0]
            qru = f"update brapci_rdf.rdf_literal set n_name = '{name2}' where id_n = {id}"
            database.update(qru)

        #print(name)


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
    if (rsp == 0):
        print("OPS Register Name")
        quit()
    return rsp
