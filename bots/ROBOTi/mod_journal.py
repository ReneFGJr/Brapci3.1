import database
import mod_data

def register(IDC,jnl):
    qr = f"select jnl_frbr from brapci.source_source where id_jnl = {jnl}"
    row = database.query(qr)

    if row != []:
        IDJ = row[0][0]

    if IDJ > 0:
        prop = 'isPartOfSource'
        mod_data.register(IDJ,prop,IDC)

    return True