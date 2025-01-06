import database
import mod_class
import mod_subject
import mod_concept
import mod_data
import requests
import mod_GoogleTranslate
import sys
from datetime import datetime

url = 'https://www.ufrgs.br/thesa/v2/index.php/api/'
th = 5
apikey = '-023- d092 -3d09 -2390d'


######################################################### AI DO THESA
def IA_thesa():
    print("IA Thesa")

    # Termo da Brapci
    ID = 249972
    ID = 174541
    IA_group_index(ID)

def IA_group_index(ID):
    # Recupera o grupo de termos
    qr = "Select c_brapci from brapci_thesa.thesa_concept "
    qr += "inner join ( "
    qr += f"SELECT c_group as groupN FROM brapci_thesa.thesa_concept WHERE c_brapci = {ID} "
    qr += ") as tabela "
    qr += "ON c_group = groupN "
    row = database.query(qr)

    print(row)
    if row == []:
        print("Nao existe grupo")
        sys.exit()

    condition = ''
    for line in row:
        if condition != '':
            condition += ' or '
        condition += f"(d_r2 = {line[0]})"

    qr = "SELECT d_r1, count(*) as total FROM brapci_rdf.rdf_data "
    qr += f"WHERE d_p = 67 and ({condition}) "
    qr += "group by d_r1;"

    print(qr)

    row2 = database.query(qr)
    for line in row2:
        IDC = line[0]
        print("IDC",IDC)
        for IDliteral in row:
            prop = 'hasSubject'
            IDL = IDliteral[0]
            IDliteral=0
            ia = 5
            mod_data.register(IDC,prop,IDL,IDliteral,ia)
        print(line)
        sys.exit()
        mod_data.register(IDC,prop,IDP,IDliteral=0,ia=0)
    print(row2)
    sys.exit()

    print(row)
    sys.exit()



    qr = "select * from brapci_thesa.thesa_literal "
    qr += " left join brapci_thesa.thesa_concept ON c_term = id_l "
    qr += " where l_lang = 'pt' "
    qr += " and id_c is null "
    qr += " limit 10 "
    row = database.query(qr)
    for line in row:
        term = line[1]
        lang = line[2]
        print("----------------")
        print(term,lang)

def translate(term,lang):

    tID = find(term,lang)
    if tID == []:
        print("Termo novo ",term,lang)
        thesa_local(term,lang)
        sys.exit()

    ID = tID[0]
    TERM = tID[1]
    LANG = tID[2]
    IDc = getThesaID(ID,ID)

    EN = checkExistConcept(IDc,'en')
    ES = checkExistConcept(IDc,'es')
    PT = checkExistConcept(IDc,'pt')

    print("TRADUCAO",EN,ES,PT,"ID:",ID,TERM,IDc)

    if not PT:
        if EN:
            termPT = mod_GoogleTranslate.translate(TERM,'pt')
            IDen = thesa_local(termPT,'pt')
            getThesaID(IDen,IDc)
            print("Tradução para o Portugues do Inglês",termPT)
        elif ES:
            termPT = mod_GoogleTranslate.translate(TERM,'pt')
            IDen = thesa_local(termPT,'pt')
            getThesaID(IDen,IDc)
            print("Tradução para o Portugues do Espanhol",termPT)

    if not EN:
        termEN = mod_GoogleTranslate.translate(TERM,'en')
        IDen = thesa_local(termEN,'en')
        getThesaID(IDen,IDc)
        print("Tradução para o [Ingles]",termEN,IDen,IDc)

    if not ES:
        termES = mod_GoogleTranslate.translate(TERM,'es')
        IDes = thesa_local(termES,'es')
        getThesaID(IDes,IDc)
        print("Tradução para o espanhol",termES)

    return IDc

def checkExistConcept(ID,lang):
    qr = "select * from brapci_thesa.thesa_concept "
    qr += f" inner join brapci_thesa.thesa_literal ON c_term = id_l and l_lang = '{lang}'"
    qr += f" where c_group = {ID}"

    row = database.query(qr)
    if row == []:
        return False
    else:
        return True


def createTerm(term,lang,th):
    return ""

def getThesaID(termID,GRP):
    qr = f"select * from brapci_thesa.thesa_concept where c_term = {termID}"
    row = database.query(qr)

    if row == []:
        return conceptRegister(termID,GRP)
    else:
        GP = row[0][2]
        IDc = row[0][0]
        if (GRP != GP):
            qu = f"update brapci_thesa.thesa_concept set c_group = {GRP} where id_c = {IDc}"
            database.update(qu)
        return row[0][0]

def conceptRegister(ID,GR):
    if (ID != GR):
        qi = "insert into brapci_thesa.thesa_concept "
        qi += "(c_thesa,c_group,c_term,c_property,c_brapci) "
        qi += " values "
        qi += f"(1,{GR},{ID},1,0)"
        database.insert(qi)
    else:
        qi = "insert into brapci_thesa.thesa_concept "
        qi += "(c_thesa,c_group,c_term,c_property,c_brapci) "
        qi += " values "
        qi += f"(1,0,{ID},1,0)"
        database.insert(qi)

        qu = "update brapci_thesa.thesa_concept set c_group = id_c where c_group = 0 "
        database.update(qu)

    return getThesaID(ID,GR)

def findConceptBrapci(term,lang):
    classe = mod_class.getClass('Subject')
    qr = "select c_group, c_brapci, id_c from brapci_thesa.thesa_literal "
    qr += f" left join brapci_thesa.thesa_concept ON c_term = id_l"
    qr += f" where l_term = '{term}' and l_lang = '{lang}'"
    row = database.query(qr)
    if row != []:
        line = row[0]

        ## ************************* Brapci
        IDbrapci = line[1]
        IDsubject = line[0]
        IDc = line[2]

        if IDbrapci == 0:
            print("================== BRAPCI")
            # Recupera ID do subject do RDF
            IDbrapci = mod_subject.findRDF(term,lang)
            if (IDbrapci == 0):
                IDbrapci = mod_concept.register_literal_class("Subject",term,lang)

            qu = "update brapci_thesa.thesa_concept "
            qu += f"set c_brapci = {IDbrapci} "
            qu += f"where id_c = {IDc} "
            database.update(qu)

            # Recupera
            row = database.query(qr)
    else:
        print("ERRO findConceptBrapci")
        sys.exit()

    return IDbrapci



def find(term,lang):
    qr = "select * from brapci_thesa.thesa_literal "
    qr += f" where l_term = '{term}' and l_lang = '{lang}'"
    row = database.query(qr)
    if row != []:
        row = row[0]
    return row

def check_subject_thesa():
    return True
    print("Check Subject - Thesa")
    IDClass = mod_class.getClass("Subject")

    qr = "select id_cc, cc_use, n_name, n_lang  "
    qr += " from brapci_rdf.rdf_concept "
    qr += " inner join brapci_rdf.rdf_literal ON id_n = cc_pref_term"
    qr += " left join brapci_thesa.thesa_literal ON l_term = n_name AND l_lang = n_lang "
    qr += f" where cc_class = {IDClass}"
    qr += " and id_l is null "
    qr += " order by n_name, id_cc"

    qr = "SELECT id_cc, cc_use, n_name, n_lang "
    qr += " FROM brapci_rdf.rdf_concept "
    qr += " INNER JOIN brapci_rdf.rdf_literal ON id_n = cc_pref_term "
    qr += " LEFT JOIN brapci_thesa.thesa_literal  "
    qr += "   ON l_term COLLATE utf8mb4_unicode_ci = n_name COLLATE utf8mb4_unicode_ci "
    qr += "      AND l_lang COLLATE utf8mb4_unicode_ci = n_lang COLLATE utf8mb4_unicode_ci "
    qr += f" WHERE cc_class = {IDClass} "
    qr += "   AND id_l IS NULL "
    qr += " ORDER BY n_name COLLATE utf8mb4_unicode_ci, id_cc;"
    #qr += " limit 200 "

    row = database.query(qr)

    for line in row:
        term = line[2]
        lang = line[3]
        id = line[0]

        dt = {}
        dt['term'] = term
        dt['lang'] = lang
        dt['th'] = th
        dt['APIKEY'] = apikey
        term = term.strip()
        thesa_local(term,lang,id)
        #thesa_api('term_add',dt)

def thesa_local(term,lang,id=0):
    term = term[:100]
    row = find(term,lang)
    now = agora = datetime.now()

    # Formata a data no formato YYYY-MM-DD
    date = now.strftime('%Y-%m-%d')

    if (row == []):
        print(term,lang,' new')
        qi = "insert into  brapci_thesa.thesa_literal "
        qi += f" (l_term,l_lang, l_update) value ('{term}','{lang}','{date}')"
        database.insert(qi)
        row = find(term,lang)
    else:
        print(term,' ########################## JA EXISTE')
    return row[0]

def thesa_register_local(term,lang,id):
    return True


def thesa_api(verb,dt=[]):
    print(url,verb)
    print(dt)

    URL = url + verb
    URL = URL.replace(' ','')
    print(URL)

    headers = {
        "Content-Type": "application/json",
        "APIKEY2": "sua_api_key_aqui"
    }

    response = requests.post(URL, headers=headers, json=dt)

    if response.status_code == 200:
        print("Success:", response.json())
    else:
        print("Failed:", response.status_code, response.text)