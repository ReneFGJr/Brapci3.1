import database
import sys

def get_statistics():
    row = database.query('SELECT count(*) as total, CLASS FROM brapci_elastic.dataset group CLASS ')
    print(row)

    qd = "delete from brapci_elastic.dataset where ind_name like 'ITEM_%'"
    database.update(qd)

    for ln in row:
        print(ln)

    sys.exit()

    return stats
