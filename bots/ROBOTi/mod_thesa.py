import database
import mod_class
import requests

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
    qr += f" where cc_class = {IDClass}"
    qr += " and id_cc = cc_use "
    qr += " and n_lang = 'pt' "
    qr += " and n_name <> '' "
    qr += " and not n_name like '%#%' "
    qr += " and not n_name like '(%' "
    qr += " and not n_name like '-%' "
    qr += " and not n_name like '&%' "
    qr += " and not n_name like ',%' "
    qr += " and not n_name like '0%' "
    qr += " and not n_name like '1%' "
    qr += " and not n_name like '2%' "
    qr += " and not n_name like '3%' "
    qr += " and not n_name like '4%' "
    qr += " and n_name like 'Biblioteca%' "

    qr += " order by n_name, id_cc"
    qr += " limit 20 "

    row = database.query(qr)

    for line in row:
        term = line[2]
        lang = line[3]
        print("===========")
        print(line[2],line[3])
        dt = {}
        dt['term'] = term
        dt['lang'] = lang
        dt['th'] = th
        dt['APIKEY'] = apikey
        thesa_api('term_add',dt)

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