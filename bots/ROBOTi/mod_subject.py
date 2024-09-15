import mod_literal
import mod_class
import mod_concept
import mod_data
import mod_thesa
import database
import mod_author
import sys

def check_remissiva():
    mod_author.check_remissiva()


################################ Recupera ID do RDF (Subect)
def findRDF(term,lang):
    IDclass = mod_class.getClass('Subject')
    qr = "SELECT id_cc FROM brapci_rdf.rdf_concept "
    qr += "inner join brapci_rdf.rdf_literal ON cc_pref_term = id_n "
    qr += f"WHERE n_name = '{term}' "
    qr += f"and n_lang = '{lang}' "
    qr += " and id_cc = cc_use "
    qr += " order by id_cc"
    qr += " limit 1 "
    row = database.query(qr)

    if row != []:
        ID = row[0][0]
    else:
        ID = 0
    return ID

def process(ID):
    print("+++++++++++++++++++++++++++++","Processar Subjects")
    IDProp = mod_class.getClass('hasSubject')
    print("=ID Class=",IDProp,ID)
    cp = "id_cc, n_name, n_lang, d_r1, d_r2"

    ############################### Recupera Termos Subject
    qr = f"select {cp} from brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_concept ON id_cc = d_r2 "
    qr += "inner join brapci_rdf.rdf_literal ON cc_pref_term = id_n "
    qr += f" where d_r1 = {ID} and d_p = {IDProp}"

    row = database.query(qr)

    keysPT = []
    keysEN = []
    keysES = []

    ### Termos
    for line in row:
        lang = line[2]
        term = line[1]
        IDc  = line[0]
        GRP  = line[3]
        if (lang == 'pt'):
            print("###### PORTUGUES")
            IDgrp = mod_thesa.translate(term,lang)

            ##### Criar conceitos no outros Idiomas
            terms = recoverTerms(IDgrp)
            prop = 'hasSubject'

            for term in terms:
                print(term)
                IDbrapci = term[1]
                termA = term[3]
                langA = term[4]
                if (IDbrapci == 0):
                    IDbrapci = mod_thesa.findConceptBrapci(termA,langA)
                    # Assicia o Artigo aos Assuntos

                ##### Registra na Brapci
                mod_data.register(ID,prop,IDbrapci,0,1)

    print("#FIM#")
    sys.exit()


    for line in row:
        term = line[1]
        lang = line[2]
        IDt = mod_thesa.translate(term,lang)
        print("=TERMO",term,lang,IDt)

        rowGR = mod_thesa.findConceptBrapci(term,lang)
        if (lang == 'pt'):
            keysPT.append(rowGR)
        print("BRAPCI ID ===",rowGR)

    print("=GR+GR+GR+G+RG+",keysPT)
    return ID

########################################################################
def recoverTerms(GRP):
        cp = "id_c, c_brapci, c_group, l_term, l_lang "
        qr = f"select {cp} from brapci_thesa.thesa_concept "
        qr += " INNER JOIN brapci_thesa.thesa_literal ON c_term = id_l"
        qr += f" where c_group = {GRP} "
        row = database.query(qr)

        return row

def register_literal(IDC,term,lg):

    IDliteral = mod_literal.register(term,lg)
    IDClass = mod_class.getClass('Subject')

    IDCt = mod_concept.register(IDClass,IDliteral)
    return mod_data.register(IDC,'hasSubject',IDCt)

def check_duplicate():
    print("Check Duplicate - Subject")
    IDClass = mod_class.getClass("Subject")

    qr = "select id_cc, cc_use, n_name, n_lang  "
    qr += " from brapci_rdf.rdf_concept "
    qr += " inner join brapci_rdf.rdf_literal ON id_n = cc_pref_term"
    qr += f" where cc_class = {IDClass}"
    qr += " and id_cc = cc_use "
    qr += " order by n_name, id_cc"

    row = database.query(qr)
    lastName = 'x'
    lastLang = 'x'
    for reg in row:
        name = reg[2]
        name = name.lower()
        name = name.replace('-','')
        name = name.replace('.','')
        name = name.strip(" ")
        lang = reg[3]

        IDn1 = reg[0]

        if ((lang == lastLang) and (name == lastName) and (name != '::Em Branco::') and (name != '(empty)')):
            print(reg[2],' =< ',name,'|',lastName)
            mod_data.remicive(IDn1,IDn2)
        else:
            reg2 = reg
            lastName = name
            lastLang = lang
            IDn2 = IDn1


def prepare(T):
    TR = []

    for i in range(len(T)):
        TE = T[i]
        lg = ''
        if '@' in TE:
            lg = TE[-2:]
            TE = TE[0:-3]

        nt = False
        if '. ' in TE:
            TE = TE.split('. ')
            for ix in range(len(TE)):
                TEe = TE[ix]
                if TEe != '':
                    TR.append([TEe,lg])
                    nt = True
        if ';' in TE:
            TE = TE.split(';')
            for ix in range(len(TE)):
                TEe = TE[ix]
                if TEe != '':
                    TR.append([TEe,lg])
                    nt = True
        if ':' in TE:
            TE = TE.split(':')
            for ix in range(len(TE)):
                TEe = TE[ix]
                if TEe != '':
                    TR.append([TEe,lg])
                    nt = True

        if nt==False:
                if TE != '':
                    TR.append([TE,lg])

    ####################### Normalize
    for i in range(len(TR)):
        T = TR[i][0]
        TR[i][0] = nbr_subject(T)

    return TR


    quit()

def nbr_subject(T):
    T = T.lower()
    M = T[0].upper()
    T = M + T[1:]
    return T

def register(T):
    print("Termos",T)
    quit()