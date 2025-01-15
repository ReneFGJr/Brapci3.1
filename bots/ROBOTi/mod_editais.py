import database
from datetime import datetime

def register(agency,title,descricao,datai, dataf, link='', status=1):
    qr = "select * from brapci_editais.editais "
    qr += f"where e_agencia = '"+str(agency)+"' and e_title = '"+title+"' "
    row = database.query(qr)

    if status == 'Aberto':
        status = 1

    if len(row) == 0:
        qr = "insert into brapci_editais.editais "
        qr += "(e_agencia,e_title,e_description,e_status, e_data_start, e_data_end, e_url) values "
        qr += "('"+str(agency)+"','"+title+"','"+descricao+"',"
        qr += "'"+str(status)+"'"
        qr += ",'"+datai+"','"+dataf+"','"+link+"')"
        database.insert(qr)
        return True
    else:
        ID = row[0][0]
        qr = "update brapci_editais.editais set "
        qr += "e_description = '"+descricao+"', "
        qr += "e_status = '"+str(status)+"', "
        qr += "e_data_start = '"+datai+"', "
        qr += "e_data_end = '"+dataf+"', "
        qr += "e_url = '"+link+"' "
        qr += "where e_agencia = '"+str(agency)+"' and e_title = '"+title+"' "
        database.update(qr)
        return False

# Função para converter o intervalo de datas em duas variáveis
def extrair_datas(intervalo):
    try:
        inicio, fim = intervalo.split(' a ')
        data_inicio = datetime.strptime(inicio.strip(), '%d/%m/%Y').strftime('%Y-%m-%d')
        data_fim = datetime.strptime(fim.strip(), '%d/%m/%Y').strftime('%Y-%m-%d')
        return data_inicio, data_fim
    except ValueError:
        return None, None