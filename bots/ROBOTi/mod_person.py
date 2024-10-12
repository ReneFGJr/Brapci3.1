import mod_class
import database
import mod_data

def check():
    print("XXX - Check Personal Name")
    IDClass = mod_class.getClass("Person")
    IDprop = mod_class.getClass("hasGender")

    qr = "select id_cc, cc_use, n_name  "
    qr += " from brapci_rdf.rdf_concept "
    qr += " inner join brapci_rdf.rdf_literal ON id_n = cc_pref_term"
    qr += f" left join brapci_rdf.rdf_data ON (d_r1 = id_cc)"
    qr += f" where cc_class = {IDClass}"
    qr += " and id_cc = cc_use "
    qr += " order by n_name, id_cc"
    qr += " limit 10000"

    row = database.query(qr)
    for line in row:
        if 'niversidade' in line[2]:
            info_autor = line[2]
            pos_hifen = info_autor.find('niversidade') - 1
            info_autor = info_autor[:pos_hifen].strip()
            info_autor = info_autor.rstrip('-').strip()
            print(line[2],pos_hifen)
            print(info_autor)
            print("======================")

def setGenere(IDC,prop,IDP):
    mod_data.register(IDC,prop,IDP,0,1)
