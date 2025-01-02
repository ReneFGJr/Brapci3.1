import database
import sys

def get_statistics():
    db = database.get_db()
    stats = database.query('SELECT count(*) as total, CLASS FROM brapci_elastic.dataset group CLASS ')
    print(stats)

    qd = "delete from brapci_elastic.dataset where ind_name like 'ITEM_%'"
    database.delete(qd)

    for ln in row:
        print(ln)

    sys.exit()

    return stats
