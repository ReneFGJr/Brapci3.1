import database
import sys

def get_statistics():
    row = database.query('SELECT count(*) as total, CLASS FROM brapci_elastic.dataset group by CLASS ')
    print(row)

    qd = "delete from brapci_elastic.dataset where ind_name like 'ITEM_%'"
    database.update(qd)

    for ln in row:
        qi = "insert into brapci.statistics (ind_name, ind_total) values ('ITEM_" + ln['CLASS'] + "', " + str(ln['total']) + ")"
        database.insert(qi)
        print(ln)

    sys.exit()

    return stats
