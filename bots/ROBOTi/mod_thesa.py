import database
import mod_class
import requests
from datetime import datetime

url = 'https://www.ufrgs.br/thesa/v2/index.php/api/'
th = 5
apikey = '-023- d092 -3d09 -2390d'

def createTerm(term,lang,th):

    return ""

def check_subject_thesa():
    print("Check Subject - Thesa")
    IDClass = mod_class.getClass("Subject")

    qr = "select id_cc, cc_use, n_name, n_lang  "
    qr += " from brapci_rdf.rdf_concept "
    qr += " inner join brapci_rdf.rdf_literal ON id_n = cc_pref_term"
    qr += " left join brapci_thesa.thesa_literal ON l_term = n_name AND l_lang = n_lang "
    qr += f" where cc_class = {IDClass}"
    qr += " and id_l is null "
    qr += " order by n_name, id_cc"
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

def thesa_local(term,lang,id):
    qr = "select * from brapci_thesa.thesa_literal "
    qr += f" where l_term = '{term}' "
    qr += f" and l_lang = '{lang}' "
    row = database.query(qr)
    now = agora = datetime.now()

    # Formata a data no formato YYYY-MM-DD
    date = now.strftime('%Y-%m-%d')

    if (row == []):
        print(term,lang,' new')
        qi = "insert into  brapci_thesa.thesa_literal "
        qi += f" (l_term,l_lang, l_update) value ('{term}','{lang}','{date}')"
        database.insert(qi)
    else:
        print(term,' ##########################')

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