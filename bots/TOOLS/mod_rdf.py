import database

def recover(id):
    qr = "select * from brapci_rdf.rdf_data "
    qr += f" where (d_r1 = {id}) or (d_r2 = {id}) "
    row = database.query(qr)
    print(row)