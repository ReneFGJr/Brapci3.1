import mod_literal
import mod_class
import mod_concept
import mod_data
import database

def check_duplicate():
    print("Check Duplicate")
    IDClass = mod_class.getClass("Person")

    qr = "select id_cc, cc_use, n_name  "
    qr += " from brapci_rdf.rdf_concept "
    qr += " inner join brapci_rdf.rdf_literal ON id_n = cc_pref_term"
    qr += f" where cc_class = {IDClass}"
    qr += " and id_cc = cc_use "
    qr += " order by n_name, id_cc"
    qr += " limit 100"

    row = database.query(qr)
    lastName = 'x'
    for reg in row:
        name=reg[2]
        IDn1 = reg[0]

        if ((name == lastName) and (name != '::Em Branco::') and (name != '(empty)')):
            print(name)
            remissive(IDn1,IDn2)

        else:
            reg2 = reg
            lastName = name
            IDn2 = IDn1

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
        print("NOME COM VIRGULA",n)
        print(n)
        quit()

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
