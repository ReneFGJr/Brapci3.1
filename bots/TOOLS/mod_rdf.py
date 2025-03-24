import database

def rdf_insert(id,Prop,id2,ID):
    qr = f"select * form brapci_rdf.rdf_data where (d_r1 = {id}) and (d_p = {Prop}) and (d_r2 = {id2}) and (d_l = {ID})"
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
