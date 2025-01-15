import database

def register(agency,title,status):
    qr = "select * from brapci_editais.editais "
    qr += f"where e_agencia = '"+str(agency)+"' and e_title = '"+title+"' "
    row = database.query(qr)

    if status == 'Aberto':
        status = 1

    if len(row) == 0:
        qr = "insert into brapci_editais.editais "
        qr += "(e_agencia,e_title,e_status) values "
        qr += "('"+str(agency)+"','"+title+"','"+str(status)+"')"
        database.query(qr)
        return True
    else:
        return False