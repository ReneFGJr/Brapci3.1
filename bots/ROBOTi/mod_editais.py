import database

def register(agency,title,descricao,status):
    qr = "select * from brapci_editais.editais "
    qr += f"where e_agencia = '"+str(agency)+"' and e_title = '"+title+"' "
    row = database.query(qr)

    if status == 'Aberto':
        status = 1

    if len(row) == 0:
        qr = "insert into brapci_editais.editais "
        qr += "(e_agencia,e_title,e_description,e_status) values "
        qr += "('"+str(agency)+"','"+title+"','"+descricao+"',"
        qr += "'"+str(status)+"')"
        database.insert(qr)
        return True
    else:
        return False