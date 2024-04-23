import mod_literal
import mod_class
import mod_concept
import mod_data
import database
import mod_logs

def check_remissiva():
    dd = 0

    qr = "SELECT id_cc, cc_use, id_n, n_name FROM brapci_rdf.rdf_concept "
    qr += "inner join brapci_rdf.rdf_literal ON cc_pref_term = id_n "
    qr += "inner join brapci_rdf.rdf_data ON d_r1 = cc_use "
    qr += "WHERE cc_use <> id_cc "
    row = database.query(qr)

    print(qr)

    for reg in row:
        ID2 = reg[0]
        ID1 = reg[1]
        NAME = reg[3]
        print("Check1 -",ID1,'<=',ID2,NAME)
        mod_data.remicive(ID1,ID2)
        quit()
        dd = dd + 1

    quit()
    qr = "SELECT id_cc,cc_use,d_r1,d_r2, n_name "
    qr += "FROM brapci_rdf.rdf_concept "
    qr += "inner join brapci_rdf.rdf_data ON id_cc = d_r2 "
    qr += "inner join brapci_rdf.rdf_literal ON cc_pref_term = id_n "
    qr += "WHERE (id_cc <> cc_use) and (cc_use <> 0)"
    qr += " and (d_r1 <> 0) and (d_r2 <> 0)"
    row = database.query(qr)

    for reg in row:
        ID2 = reg[0]
        ID1 = reg[1]
        NAME = reg[4]
        print("Check2 -",ID1,ID2,NAME)
        mod_data.remicive(ID1,ID2)
        dd = dd + 1

    mod_logs.log('TASK_201',dd)
    return ""

def check_duplicate():
    print("Check Duplicate")
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
        name = name.replace(' da ',' ')
        name = name.replace(' dos ',' ')
        name = name.replace(' e ',' ')
        name = name.replace('  ',' ')
        name = name.replace('  ',' ')

        IDn1 = reg[0]

        #if name[0] == 'G':
        #    print(name,'|',lastName)

        if ((name == lastName) and (name != '::Em Branco::') and (name != '(empty)')):
            print(name)
            remissive(IDn1,IDn2)
            dd = dd + 1
        else:
            reg2 = reg
            lastName = name
            IDn2 = IDn1
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
