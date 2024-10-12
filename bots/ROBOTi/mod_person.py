import mod_class
import database
import mod_data
import mod_literal

def check():
    print("XXX - Check Personal Name")
    IDClass = mod_class.getClass("Person")
    IDprop = mod_class.getClass("hasGender")

    qr = "select id_cc, cc_use, n_name, id_n  "
    qr += " from brapci_rdf.rdf_concept "
    qr += " inner join brapci_rdf.rdf_literal ON id_n = cc_pref_term"
    qr += f" left join brapci_rdf.rdf_data ON (d_r1 = id_cc)"
    qr += f" where cc_class = {IDClass}"
    qr += " and id_cc = cc_use "
    qr += " order by n_name, id_cc"

    row = database.query(qr)
    for line in row:
        up = False
        info_autor = line[2]
        idn = line[3]

        if '-Centro' in line[2]:
            pos_hifen = info_autor.find('-Centro')
            info_autor = info_autor[:pos_hifen].strip()
            up = True

        if 'niversid' in line[2]:
            pos_hifen = info_autor.find('niversid') - 1
            info_autor = info_autor[:pos_hifen].strip()
            up = True

        if up == True:
            info_autor = info_autor.rstrip('-').strip()
            mod_literal.update_term(idn,info_autor)
            print(line[2],pos_hifen)
            print(info_autor)
            print("======================")

def setName(IDC,prop,IDP):
    mod_data.register(IDC,prop,IDP,0,1)
