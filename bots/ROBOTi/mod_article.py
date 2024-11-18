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
import sys

def process(rg):
    ID = rg[0]
    JNL = rg[6]
    IDA = rg[1]
    TYPE = rg[11]
    IDX = rg[5]

    print(Fore.YELLOW+f"... Processando ISSUE {TYPE} ({ID}): "+Fore.GREEN+rg[1]+Fore.WHITE)
    print("ID",ID)
    path = mod_listidentify.directory(ID)+'.getRecord.json'

    try:
        ##print(path)
        f = open(path)
        data = json.load(f)
        f.close()

        #Verifica se existe o ID = METHODO 01
        BYPASS = 0

        if IDX == 0:
            print("Method 01")
            IDX = check_method01(IDA,JNL)

            print("+============================")
            print(IDX,IDA,JNL)
            sys.exit()

        if (IDX > 0) and (BYPASS == 1):
            print(f"===Method #01 ({IDX}={ID})")
            mod_listidentify.updateRDF(ID,IDX)
            mod_listidentify.updateStatus(ID,11)
            return ""


        if (IDX == 0):
            print("Method 02")
            IDX = check_method02(data,IDA,JNL)
            if IDX == 0:
                print("================== NAO FOI POSSIVEL IDENTIFICAD O METODO #2")
            else:
                print("IDX==",IDX)

        ########################################## Inserir Trabalho
        if (IDX == 0):
            if (TYPE == 'EV'):
                print("  CREATE WORK - PROCEEDING")
                IDX = create_proceeding(rg,data,JNL)
            else:
                print("  CREATE WORK - ARTICLE")
                IDX = create_article(rg,data,JNL)
            mod_listidentify.updateStatus(ID,10)
        else:
            print(f"  UPDATE WORK ({IDX})")
            article_data(IDX,rg,data,JNL)
            mod_listidentify.updateStatus(ID,12)

    except Exception as e:
        mod_listidentify.updateStatus(ID,1)
        print("ERRO ARTICLE",e)

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

    print(qr)
    row = database.query(qr)

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
    jnl = data[0]
    IDjnl = jnl['journal']['id_jnl']
    print("...Mtd02-Title")
    title = data[2]

    TITLE = title['title'][0]
    TITLE = TITLE.replace('@pt','')
    TITLE = TITLE.replace('@en','')
    TITLE = TITLE.replace('@es','')

    issue = data[6]
    print("...Mtd02-ISSUE")
    SOURCE = issue['source']
    YEAR = SOURCE['year']


    print(f"...Mtd02-Dataset\nTitle:{TITLE}\nYear:{YEAR}")
    qr = f"select ID from brapci_elastic.dataset "
    qr += f" where TITLE = '{TITLE}' and JOURNAL = {IDjnl}"
    qr += f" and YEAR = '{YEAR}' "
    row = database.query(qr)

    if row == []:
        return 0
    else:
        ID = row[0]
        ID = ID[0]
        return ID


############################################## CONCEPT
def create_proceeding(rg,data,jnl):
    create_article(rg,data,jnl,'Proceeding')

def create_article(rg,data,jnl,Class='Article'):
    #Monta ID do trabalho com o ID do Journal
    jnl = str(jnl)
    while (len(jnl) < 5):
        jnl = "0"+jnl

    id = rg[1]
    idOCS = rg[1]

    ID = id + "#"+jnl

    IDClass = mod_class.getClass(Class)
    ##################################### Registra o Literal
    IDliteral = mod_literal.register(ID,'nn')

    ##################################### Create Concept
    IDC = mod_concept.register(IDClass,IDliteral)
    print("..CREATE #",ID,IDClass,IDliteral,"IDC:",IDC)

    ##################################### OCS ID
    print("..Registres",IDC,idOCS)
    mod_data.register_literal(IDC,'hasID',idOCS,'nn')
    article_data(IDC,rg,data,jnl)

def article_data(IDC,rg,data,jnl):

    # ISSUE ########################################################### ISSUE
    row = mod_issue.identify(rg)
    if row != []:
        IDissue = row[0][3]
        if (IDissue > 0):
            print("...ISSUE-WORK")
            mod_data.register(IDissue,'hasIssueOf',IDC)
            mod_source_issue_work.register(jnl,IDissue,IDC)
        else:
            print("Erro ISSUE inválido");
            quit()
            create_issue_rdf
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
                print("...ISSUE-WORK-TITLE")
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    lgs = mod_language.detect(T[it])
                    mod_data.register_literal(IDC,'hasTitle',lgs[0],lgs[1])

            #################################### Abstract
            if (k == 'abstract'):
                print("...ISSUE-WORK-ABSTRACT")
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    lgs = mod_language.detect(T[it])
                    mod_data.register_literal(IDC,'hasAbstract',lgs[0],lgs[1])
                    print("Abstract",IDC)

            #################################### URL
            if (k == 'http'):
                print("...ISSUE-WORK-HTTP")
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    URL = T[it]['value']
                    URL = URL[0]
                    mod_data.register_literal(IDC,'hasUrl',URL,'nn')

            #################################### DOI
            if (k == 'DOI'):
                print("...ISSUE-WORK-DOI")
                ok = True
                T = data[i][k]
                for it in range(len(T)):

                    DOI = T[it]['value']
                    DOI = DOI[0]
                    mod_data.register_literal(IDC,'hasDOI',DOI,'nn')

            ######################################################## Concepts

            #################################### Subject
            if (k == 'subject'):
                print("...ISSUE-WORK-SUBJECT")
                ok = True
                T = data[i][k]
                T = mod_subject.prepare(T)

                for it in range(len(T)):
                    mod_subject.register_literal(IDC,T[it][0],T[it][1])

            #################################### Author
            if (k == 'author'):
                print("...ISSUE-WORK-AUTHOR")
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    mod_author.register_literal(IDC,T[it])

            #################################### Licence
            if (k == 'license'):
                ok = True
                T = data[i][k]
                print("...ISSUE-WORK-LICENSE")
                for it in range(len(T)):
                    mod_license.register_literal(IDC,T[it])

            #################################### datePub
            if (k == 'datePub'):
                print("...ISSUE-WORK-DATEPUB")
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    mod_generic.register_literal(IDC,T[it],'nn','Date','wasPublicationInDate')

            #################################### Language
            if (k == 'language'):
                print("...ISSUE-WORK-LANGUAGE")
                ok = True
                T = data[i][k]
                for it in range(len(T)):
                    mod_generic.register_literal(IDC,T[it],'nn','Language','hasLanguageExpression')


            #################################### Journal
            if (k == 'journal'):
                print("...ISSUE-WORK-JOURNAL")
                ok = True
                J = data[i][k]
                J = J['id_jnl']
                mod_journal.register(IDC,J)

            #################################### Journal
            if (k == 'section'):
                ok = True
                S = data[i][k]
                ids = S['section']
                print("...ISSUE-WORK-SECTION",ids)

                qr = "select sc_rdf, sc_name, s_section from brapci_oaipmh.oai_setspec "
                qr += "left join brapci.sections on s_section = id_sc "
                qr += f"where id_s = {ids}"

                row = database.query(qr)
                IDsec = row[0][0]

                if (IDsec == None or IDsec < 1):
                    print("Erro Section IDsec="+IDsec)
                    sys.exit()
                    quit()
                else:
                    print("...=>Section",IDsec)
                    mod_data.register(IDC,'hasSectionOf',IDsec)
                    print("...=>Section (FIM)",IDsec)

            #################################### Source ISSUE
            if (k == 'source'):
                print("...ISSUE-WORK-SOURCE")
                T = data[i][k]
                ok = True

            if ok == 0:
                print(f'RSP: {k}')
                quit()
    #################################### Title
    mod_listidentify.updateStatus(rg[0],11)
    print("============================ FINALIZADO COM SUCESSO",rg[0],"IDC ",IDC)
