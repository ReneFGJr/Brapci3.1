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
import mod_section
import mod_issue
import mod_source_issue_work
import database

def process(rg):
    ID = rg[0]
    JNL = rg[6]
    IDA = rg[1]

    print(Fore.YELLOW+f"... Processando ISSUE ARTICLE ({ID}): "+Fore.GREEN+rg[1]+Fore.WHITE)
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
            mod_listidentify.updateStatus(ID,8)

    except Exception as e:
        mod_listidentify.updateStatus(ID,1)
        print("ERRO ARTICLE",e)

def check_method01(id,jnl):
    print("Method 01")
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

    print("ROW",len(row))

    if row == []:
        return 0

    if (len(row) == 1):
        return row[0][0]
    else:
        if (len(row) > 0):
            return row[0][0]

    print("NENHUM IDC")
    print(row)
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
    idOCS = rg[1]

    ID = id + "#"+jnl

    IDClass = mod_class.getClass('Article')
    ##################################### Registra o Literal
    IDliteral = mod_literal.register(ID,'nn')

    ##################################### Create Concept
    IDC = mod_concept.register(IDClass,IDliteral)
    print("CREATE #",ID,IDClass,IDliteral,"IDC:",IDC)

    ##################################### OCS ID
    print("Registres",IDC,idOCS)
    mod_data.register_literal(IDC,'hasID',idOCS,'nn')

    # ISSUE ########################################################### ISSUE
    row = mod_issue.identify(rg)
    if row != []:
        IDissue = row[0][3]
        if (IDissue > 0):
            print("ISSE-WORK")
            mod_data.register(IDissue,'hasIssueOf',IDC)
            mod_source_issue_work.register(jnl,IDissue,IDC)
        else:
            print("Erro ISSUE inválido");
            quit()
    else:
        print("OPS")
        quit()
    # DATA ############################################################ DATA

    for i in range(len(data)):
        keys = data[i].keys()
        for k in keys:
            ok = 0
            #################################### TITLE
            if (k == 'title'):
                print("ISSE-WORK-TITLE")
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    lgs = mod_language.detect(T[it])
                    mod_data.register_literal(IDC,'hasTitle',lgs[0],lgs[1])

            #################################### Abstract
            if (k == 'abstract'):
                print("ISSE-WORK-ABSTRACT")
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    lgs = mod_language.detect(T[it])
                    mod_data.register_literal(IDC,'hasAbstract',lgs[0],lgs[1])
                    print("Abstract",IDC)

            #################################### URL
            if (k == 'http'):
                print("ISSE-WORK-HTTP")
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    URL = T[it]['value']
                    URL = URL[0]
                    mod_data.register_literal(IDC,'hasUrl',URL,'nn')

            #################################### DOI
            if (k == 'DOI'):
                print("ISSE-WORK-DOI")
                ok = True
                T = data[i][k]
                for it in range(len(T)):

                    DOI = T[it]['value']
                    DOI = DOI[0]
                    mod_data.register_literal(IDC,'hasDOI',DOI,'nn')

            ######################################################## Concepts

            #################################### Subject
            if (k == 'subject'):
                print("ISSE-WORK-SUBJECT")
                ok = True
                T = data[i][k]
                T = mod_subject.prepare(T)

                for it in range(len(T)):
                    mod_subject.register_literal(IDC,T[it][0],T[it][1])

            #################################### Author
            if (k == 'author'):
                print("ISSE-WORK-AUTHOR")
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    mod_author.register_literal(IDC,T[it])

            #################################### Licence
            if (k == 'license'):
                ok = True
                T = data[i][k]
                print("ISSE-WORK-LICENSE")
                for it in range(len(T)):
                    mod_license.register_literal(IDC,T[it])

            #################################### datePub
            if (k == 'datePub'):
                print("ISSE-WORK-DATEPUB")
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    mod_generic.register_literal(IDC,T[it],'nn','Date','wasPublicationInDate')

            #################################### Language
            if (k == 'language'):
                print("ISSE-WORK-LANGUAGE")
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    mod_generic.register_literal(IDC,T[it],'nn','Language','hasLanguageExpression')


            #################################### Journal
            if (k == 'journal'):
                print("ISSE-WORK-JOURNAL")
                ok = True
                J = data[i][k]
                J = J['id_jnl']
                mod_journal.register(IDC,J)

            #################################### Journal
            if (k == 'section'):
                print("ISSE-WORK-SECTION")
                ok = True
                S = data[i][k]
                ids = S['section']

                qr = "select sc_rdf, sc_name, s_section from brapci_oaipmh.oai_setspec "
                qr += "left join brapci.sections on s_section = id_sc "
                qr += f"where id_s = {ids}"
                row = database.query(qr)
                IDsec = row[0][0]
                if (IDsec == None or IDsec < 1):
                    print("Erro Section")
                    quit()
                else:
                    print("=>Section",IDsec)
                    mod_data.register(IDC,'hasSectionOf',IDsec)
                    print("=>Section (FIM)",IDsec)

            #################################### Source ISSUE
            if (k == 'source'):
                print("ISSE-WORK-SOURCE")
                T = data[i][k]
                ok = True

            if ok == 0:
                print(f'RSP: {k}')
                quit()
    #################################### Title
    mod_listidentify.updateStatus(rg[0],10)
