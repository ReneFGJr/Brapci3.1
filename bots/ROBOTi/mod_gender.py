import mod_class
import database
import requests
import urllib.parse
import mod_data

def check():
    print("XXX - Check Personal Gender")
    IDClass = mod_class.getClass("Person")
    IDprop = mod_class.getClass("hasGender")

    qr = "select id_cc, cc_use, n_name  "
    qr += " from brapci_rdf.rdf_concept "
    qr += " inner join brapci_rdf.rdf_literal ON id_n = cc_pref_term"
    qr += f" left join brapci_rdf.rdf_data ON (d_r1 = id_cc) and (d_p = {IDprop})"
    qr += f" where cc_class = {IDClass}"
    qr += " and id_cc = cc_use "
    qr += " and id_d is null "
    qr += " order by n_name, id_cc"
    qr += " limit 10000"

    row = database.query(qr)
    masculino = 99567
    feminino = 99568
    indefinido = 99569

    for line in row:
        url = 'https://cip.brapci.inf.br/api/gender?name=' + urllib.parse.quote(line[2])

        try:
            rsp = requests.get(url)
            if rsp.status_code == 200:
                # A resposta é uma string
                gender_data = rsp.text

                if (gender_data == 'indefinido'):
                    setGenere(line[1],'hasGender',indefinido)
                if (gender_data == 'masculino'):
                    setGenere(line[1],'hasGender',masculino)
                if (gender_data == 'feminino'):
                    setGenere(line[1],'hasGender',feminino)
                print(f"=I=> {line[2]}: {gender_data}")
            else:
                print(f"Error: API request failed with status code {rsp.status_code}")
        except Exception as e:
            print(f"Exception occurred during API request: {str(e)}")

def setGenere(IDC,prop,IDP):
    mod_data.register(IDC,prop,IDP,0,1)
