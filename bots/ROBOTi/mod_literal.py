import database

def check_trim():
    qr = f"select id_n,n_name from brapci_rdf.rdf_literal where (n_name like ' %')"
    row = database.query(qr)
    for ln in row:
        name = ln[1]
        name = name.strip()
        id = ln[0]
        qru = f"update brapci_rdf.rdf_literal set n_name = '{name}' where id_n = {id}"
        print(qru)

def register(term,lang):
    qr = f"select * from brapci_rdf.rdf_literal where (n_name = '{term}') and (n_lang = '{lang}')"
    row = database.query(qr)
    if row == []:
        qri = "insert into brapci_rdf.rdf_literal "
        qri += '(n_name, n_lock, n_lang)'
        qri += ' values '
        qri += f"('{term}',1,'{lang}')"
        row = database.query(qri)
        row = database.query(qr)
    rsp = row[0][0]
    if (rsp == 0):
        print("OPS Register Name")
        quit()
    return rsp
