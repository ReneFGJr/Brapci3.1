import database
import sys

def get_statistics():
    db = database.get_db()
    stats = database.query('SELECT count(*) as total, CLASS FROM brapci_elastic.dataset group CLASS ')
    print(stats)
    sys.exit()
    return stats
