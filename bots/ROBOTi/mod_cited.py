import database

def cited():
    qr = "select id_n, d_r1, n_name from brapci_rdf.rdf_literal "
    qr += "inner join brapci_rdf.rdf_data ON id_n = d_literal "
    qr += "where n_name like '%/*ref*/%'"
    row = database.query(qr)

    for item in row:
        print(item)
        ID = item[1]
        IDn = item[0]
        REF = item[2]

        REF = REF.replace('/*ref*/','').strip()
        register(ID,REF)
        print(ID,IDn,REF)
        quit()

def register(ID,REF):
    qr = "select * from brapci_cited.cited_article "
    qr += f" where ca_text = '{REF}' "
    qr += f" and ca_rdf = {ID}"
    row = database.query(qr)
    if not row:
        print("NOVO")
        qi = "insert into brapci_cited.cited_article "
        qi += "(ca_rdf, ca_text, ca_status) "
        qi += " values "
        qi += "('{ID}','^{REF}',0)"
        database.insert(qr)
