import database
import sys

def rdf_insert_concept(Classe,pref_term):
    qr = f"select * from brapci_rdf.rdf_literal where (n_name = '{pref_term}')"
    row = database.query(qr)
    if row == []:
        qr = f"insert into brapci_rdf.rdf_literal (n_name,n_lang) values ('{pref_term}','nn')"
        database.update(qr)
        qr = f"select id_n from brapci_rdf.rdf_literal where n_name = '{pref_term}'"
        row = database.query(qr)
        idT = row[0][0]
    else:
        idT = row[0][0]

    ########################################################## Concept
    qr = f"select * from brapci_rdf.rdf_concept where cc_class = {Classe} and cc_pref_term = {idT}"
    row = database.query(qr)
    if row == []:
        qr = f"insert into brapci_rdf.rdf_concept (cc_class,cc_pref_term) values ({Classe},{idT})"
        database.insert(qr)

        qr = f"select * from brapci_rdf.rdf_concept where cc_class = {Classe} and cc_pref_term = {idT}"
        row = database.query(qr)
    print(row)
    sys.exit(0)


    print(qr)

def rdf_insert(id,Prop,id2,ID):
    qr = f"select * from brapci_rdf.rdf_data where (d_r1 = {id}) and (d_p = {Prop}) and (d_r2 = {id2}) and (d_literal = {ID})"
    row = database.query(qr)
    if row == []:
        qr = f"insert into brapci_rdf.rdf_data (d_r1,d_p,d_r2,d_literal) values ({id},{Prop},{id2},{ID})"
        database.update(qr)
    return

def recover(id,classe):
    qr = "select c_class, d_r1, d_r2, n_name, n_lang from brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_class on d_p = id_c "
    qr += "left join brapci_rdf.rdf_literal on d_literal = id_n "
    qr += f" where (d_r1 = {id}) or (d_r2 = {id}) "
    row = database.query(qr)

    rst = []

    for line in row:
        Xclasse = line[0]
        if classe == Xclasse:
            idR = line[2]
            rst.append(idR)
    return rst

def le(id):
    qr = "select c_class, d_r1, d_r2, n_name, n_lang from brapci_rdf.rdf_data "
    qr += "left join brapci_rdf.rdf_class on d_p = id_c "
    qr += "left join brapci_rdf.rdf_literal on d_literal = id_n "
    qr += f" where (d_r1 = {id}) "
    row = database.query(qr)

    qr = "select id_cc, n_name, n_lang, id_n from brapci_rdf.rdf_concept "
    qr += "left join brapci_rdf.rdf_class on cc_class = id_c "
    qr += "left join brapci_rdf.rdf_literal on cc_pref_term = id_n "
    qr += f" where (id_cc = {id}) "
    row2 = database.query(qr)

    dt = {'concept':[], 'data':[]}
    dt['concept'] = row2
    dt['data'] = row

    return dt
