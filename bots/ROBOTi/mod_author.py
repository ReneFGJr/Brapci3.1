import mod_literal
import mod_class
import mod_concept
import mod_data
import database
import mod_logs

def check_use_zero():
    qr = "update "
    qr += "brapci_rdf.rdf_concept "
    qr += "set cc_use = id_cc "
    qr += "where cc_use = 0 "
    database.update(qr)

def check_dupla_remissiva():
    check_use_zero()
    print("201 - Check Dupla Remissiva")
    dd = 0
    qr = "SELECT c1.id_cc, c2.id_cc, c2.cc_use "
    qr += "FROM brapci_rdf.rdf_concept as c1 "
    qr += "INNER JOIN brapci_rdf.rdf_concept as c2 ON c1.cc_use = c2.id_cc "
    qr += "WHERE c1.cc_use <> c1.id_cc "
    qr += "and c2.id_cc <> c2.cc_use "

    row = database.query(qr)

    for reg in row:
        ID2 = reg[0]
        ID1 = reg[2]
        print("CheckD -",ID1,'<=',ID2)
        mod_data.remicive(ID1,ID2)
        dd = dd + 1

    qd = "COMMIT"
    database.update(qd)

    mod_logs.log('TASK_202',dd)

def check_remissiva_old():
    print("202 - Check author remissive")

    dd = 0

    qr = "SELECT id_cc, cc_use, id_n, n_name FROM brapci_rdf.rdf_concept "
    qr += "inner join brapci_rdf.rdf_literal ON cc_pref_term = id_n "
    qr += "inner join brapci_rdf.rdf_data ON ((d_r1 = id_cc) and (d_literal = 0)) "
    qr += "WHERE (cc_use <> id_cc) and (d_r2 > 0) and (cc_use <> 0)"
    print(qr)
    row = database.query(qr)

    ID2A = 0
    ID1A = 0

    for reg in row:
        ID2 = reg[0]
        ID1 = reg[1]
        NAME = reg[3]
        if (ID1 != ID1A) and (ID2A != ID2):
            print("Check1 -",ID1,'<=',ID2,NAME)
            mod_data.remicive(ID1,ID2)
            dd = dd + 1
        ID2A = ID2
        ID1A = ID1

    qd = "COMMIT"
    database.update(qd)

    mod_logs.log('TASK_202',dd)
    return ""

def check_remissiva():
    check_use_zero()
    print("202 - Check author remissive forgot")

    dd = 0

    qr = "SELECT id_cc, cc_use, id_n, n_name FROM brapci_rdf.rdf_concept "
    qr += "inner join brapci_rdf.rdf_literal ON cc_pref_term = id_n "
    qr += "inner join brapci_rdf.rdf_data ON ((d_r1 = cc_use) and (d_literal = 0)) "
    qr += "WHERE (cc_use <> id_cc) and (d_r2 > 0) and (cc_use <> 0)"
    print(qr)
    row = database.query(qr)

    ID2A = 0
    ID1A = 0

    for reg in row:
        ID2 = reg[0]
        ID1 = reg[1]
        NAME = reg[3]
        if (ID1 != ID1A) and (ID2A != ID2):
            print("Check1 -",ID1,'<=',ID2,NAME)
            mod_data.remicive(ID1,ID2)
            dd = dd + 1
        ID2A = ID2
        ID1A = ID1

    qd = "COMMIT"
    database.update(qd)

    mod_logs.log('TASK_202',dd)
    return ""

def check_duplicate():
    print("200 - Check Duplicate Literal")
    IDClass = mod_class.getClass("Person")

    qr = "select id_cc, cc_use, n_name  "
    qr += " from brapci_rdf.rdf_concept "
    qr += " inner join brapci_rdf.rdf_literal ON id_n = cc_pref_term"
    qr += f" where cc_class = {IDClass}"
    qr += " and id_cc = cc_use "
    qr += " order by n_name, id_cc"

    row = database.query(qr)
    lastName = 'x'
    dd = 0
    for reg in row:
        name = reg[2]
        name = name.replace('-',' ')
        name = name.replace(' de ',' ')
        name = name.replace(' De ',' ')
        name = name.replace(' da ',' ')
        name = name.replace(' Da ',' ')
        name = name.replace(' do ',' ')
        name = name.replace(' Do ',' ')
        name = name.replace(' dos ',' ')
        name = name.replace(' Dos ',' ')
        name = name.replace(' e ',' ')
        name = name.replace('  ',' ')
        name = name.replace('  ',' ')

        IDn1 = reg[0]

        if ((name == lastName) and (name != '::Em Branco::') and (name != '(empty)')):
            print(name)
            remissive(IDn1,IDn2)
            dd = dd + 1
        else:
            reg2 = reg
            lastName = name
            IDn2 = IDn1

    qd = "COMMIT"
    database.update(qd)

    mod_logs.log('TASK_200',dd)

def remissive(ID1,ID2):
    if ID2 < ID1:
        ID3 = ID1
        ID1 = ID2
        ID2 = ID3
    mod_data.remicive(ID1,ID2)

def register_literal(IDC,name):
    name = nbr_author(name)

    IDliteral = mod_literal.register(name,'nn')
    IDClass = mod_class.getClass('Person')

    IDCt = mod_concept.register(IDClass,IDliteral)
    return mod_data.register(IDC,'hasAuthor',IDCt)

def nbr_author(n):
    if ',' in n:
        n = n.replace(',','')
        print("NOME COM VIRGULA",n)

    nm = n.lower()
    nm = nm.split(' ')

    pre = ['de','da','e','do']

    n = ''
    for i in range(len(nm)):
        na = nm[i]
        na1 = na[:1]
        na2 = na[1:]

        for x in range(len(pre)):
            if na == pre[x]:
                na2 = na
                na1 = ''
        if n != '':
            n += ' '
        n += na1.upper()+na2
    print(n)
    return n
