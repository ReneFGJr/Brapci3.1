import database
import sys
from datetime import datetime

def get_statistics():
    row = database.query('SELECT count(*) as total, CLASS FROM brapci_elastic.dataset group by CLASS ')
    print(row)

    qd = "delete from brapci.statistics where ind_name like 'ITEM_%'"
    database.update(qd)

    data_atual = datetime.now()
    row.append(data_atual.strftime("%d/%m/%Y"), 'UPDATE')

    for ln in row:
        qi = "insert into brapci.statistics (ind_name, ind_total) values ('ITEM_" + ln[1] + "', " + str(ln[0]) + ")"
        database.insert(qi)
        print(ln)

    sys.exit()

    return stats
