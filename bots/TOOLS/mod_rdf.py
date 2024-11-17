import database

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
    qr += "inner join brapci_rdf.rdf_class on d_p = id_c "
    qr += "left join brapci_rdf.rdf_literal on d_literal = id_n "
    qr += f" where (d_r1 = {id}) or (d_r2 = {id}) "
    row = database.query(qr)

    return row
