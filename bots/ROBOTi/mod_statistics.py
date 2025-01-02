import database
from datetime import datetime

def get_statistics():
    # Obter estatísticas do banco de dados
    row = database.query('SELECT count(*) as total, CLASS FROM brapci_elastic.dataset GROUP BY CLASS')
    if not row:
        print("Nenhuma estatística encontrada.")
        return

    print("Estatísticas obtidas:", row)

    # Limpar estatísticas antigas
    qd = "DELETE FROM brapci.statistics WHERE ind_name LIKE 'ITEM_%'"
    database.update(qd)

    # Adicionar a linha de atualização com a data atual
    data_atual = datetime.now().strftime("%d/%m/%Y")
    rows_to_insert = row + [(data_atual, 'UPDATE')]

    # Tipologias
    qr = "SELECT count(*) as total, c_class, id_c "
    qr += "FROM brapci_rdf.rdf_concept "
    qr += " inner join brapci_rdf.rdf_class ON cc_class = id_c "
    qr += " where cc_use = id_cc "
    qr += " and ((id_c = 50) or (id_c = 13) or (id_c = 50) or (id_c = 9)) "
    qr += " group by c_class, id_c "
    qr += " ORDER BY c_class ASC "
    row2 = database.query(qr)
    rows_to_insert = row + row2

    # Inserir novas estatísticas
    for ln in rows_to_insert:
        # Construir os valores para inserção
        ind_name = f"ITEM_{ln[1]}"
        ind_total = ln[0]

        qi = f"INSERT INTO brapci.statistics (ind_name, ind_total) VALUES ('{ind_name}', '{ind_total}')"
        database.insert(qi)
        print(f"Registro inserido: ind_name={ind_name}, ind_total={ind_total}")


    print("Atualização de estatísticas concluída.")
