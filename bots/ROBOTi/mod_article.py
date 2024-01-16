import re
import json
import string
import array
from colorama import Fore
import mod_author
import mod_listidentify
import mod_literal
import mod_license
import mod_concept
import mod_language
import mod_generic
import mod_class
import mod_data
import mod_journal
import mod_subject
import database

def process(rg):
    ID = rg[0]
    JNL = rg[6]
    IDA = rg[1]

    print(Fore.YELLOW+f"... Processando ISSUE ({ID}): "+Fore.GREEN+rg[1]+Fore.WHITE)
    path = mod_listidentify.directory(ID)+'.getRecord.json'

    try:
        ##print(path)
        f = open(path)
        data = json.load(f)
        f.close()

        #Verifica se existe o ID = METHODO 01
        IDX = check_method01(IDA,JNL)
        if IDX > 0:
            mod_listidentify.updateRDF(ID,IDX)
            mod_listidentify.updateStatus(ID,8)
            return ""

        print("Method 02")
        IDX = check_method02(data,IDA,JNL)
        if IDX == 0:
            print("================== NAO EXISTE")
        else:
            print("IDX==",IDX)


        ########################################## Inserir Trabalho
        print("IDX",IDX)

        if (IDX == 0):
            print("CREATE WORK")
            IDX = create_article(rg,data,JNL)
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
    return 0

    title = []

    for i in range(len(data)):
        keys = data[i].keys()
        for k in keys:
            ##print(f'RSP: {k}')
            if (k == 'title'):
                ##print("HELLO",k,i)
                title = data[i][k]

    for i in range(len(title)):
        if '@' in title[i]:
            title[i] = title[i][:-3]

    ################################### Verifica se não existe cadastrado
    ## Method 01 - ID

    qr = "select id_cc from brapci_rdf.rdf_literal "
    qr += f"inner join brapci_rdf.rdf_data ON d_literal = id_n "
    qr += f"inner join brapci_rdf.rdf_concept ON d_r1 = id_cc "
    ## Phase I
    for i in range(len(title)):
        TITLE = title[i]
        if i==0:
            qr += f"where n_name = '{TITLE}'"
        else:
            qr += f"OR n_name = '{TITLE}'"
    qr += "group by id_cc"
    row = database.query(qr)

    print("MTH2=>",row)

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

############################################## CONCEPT
def create_article(rg,data,jnl):
    #Monta ID do trabalho com o ID do Journal
    jnl = str(jnl)
    while (len(jnl) < 5):
        jnl = "0"+jnl

    id = rg[1]
    ID = id + "#"+jnl

    IDClass = mod_class.getClass('Article')

    ##################################### Registra o Literal
    IDliteral = mod_literal.register(ID,'nn')

    ##################################### Create Concept
    IDC = mod_concept.register(IDClass,IDliteral)
    print("CREATE",ID,IDClass,IDliteral,IDC)

    for i in range(len(data)):
        keys = data[i].keys()
        for k in keys:
            ok = 0
            #################################### TITLE
            if (k == 'title'):
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    lgs = mod_language.detect(T[it])
                    mod_data.register_literal(IDC,'hasTitle',lgs[0],lgs[1])

            #################################### Abstract
            if (k == 'abstract'):
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    lgs = mod_language.detect(T[it])
                    mod_data.register_literal(IDC,'hasAbstract',lgs[0],lgs[1])
                    print("Abstract",IDC)

            #################################### URL
            if (k == 'http'):
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    URL = T[it]['value']
                    URL = URL[0]
                    mod_data.register_literal(IDC,'hasUrl',URL,'nn')

            #################################### DOI
            if (k == 'DOI'):
                ok = True
                T = data[i][k]
                for it in range(len(T)):

                    DOI = T[it]['value']
                    DOI = DOI[0]
                    print("DOI",DOI)
                    mod_data.register_literal(IDC,'hasDOI',DOI,'nn')

            ######################################################## Concepts

            #################################### Subject
            if (k == 'subject'):
                ok = True
                T = data[i][k]
                T = mod_subject.prepare(T)

                for it in range(len(T)):
                    mod_subject.register_literal(IDC,T[it][0],T[it][1])

            #################################### Author
            if (k == 'author'):
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    mod_author.register_literal(IDC,T[it])

            #################################### Licence
            if (k == 'license'):
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    mod_license.register_literal(IDC,T[it])

            #################################### datePub
            if (k == 'datePub'):
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    mod_generic.register_literal(IDC,T[it],'nn','Date','wasPublicationInDate')

            #################################### Language
            if (k == 'language'):
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    mod_generic.register_literal(IDC,T[it],'nn','Language','hasLanguageExpression')


            #################################### Journal
            if (k == 'journal'):
                ok = True
                J = data[i][k]
                J = J['id_jnl']
                mod_journal.register(IDC,J)

            #################################### Journal
            if (k == 'section'):
                #ok = True
                S = data[i][k]
                print(S['section']);
                quit()
                #mod_journal.register(IDC,J)

            #################################### Source ISSUE
            if (k == 'sourceX'):
                T = data[i][k]
                print(T)
                quit()
                for it in range(len(T)):
                    lgs = mod_language.detect(T[it])
                    mod_data.register_literal(IDC,'hasAbstract',lgs[0],lgs[1])

            if ok == 0:
                print(f'RSP: {k}')
    #################################### Title
    print(IDC)
    quit()