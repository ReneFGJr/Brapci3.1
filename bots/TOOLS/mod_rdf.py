import database

def recover(id):
    qr = "select cc_class, d_r1, d_r2 from brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_class on d_p = id_c "
    qr += f" where (d_r1 = {id}) or (d_r2 = {id}) "
    row = database.query(qr)
    print(row)