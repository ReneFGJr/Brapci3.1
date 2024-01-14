import database

def register(cl,literal):

    qr = "select * from brapci_rdf.rdf_concept "
    qr += f" where (cc_class = {cl}) and (cc_pref_term = {literal})"
    row = database.query(qr)

    if row == []:
        qri = 'insert into brapci_rdf.rdf_concept '
        qri += "(cc_class , cc_use , c_equivalent, cc_pref_term , cc_origin , cc_status , cc_version, cc_update )"
        qri += " values "
        qri += f"({cl},0,0,{literal},'',0,2,'2000-01-01')"
        database.query(qri)
        row = database.query(qr)
    return row[0][0]