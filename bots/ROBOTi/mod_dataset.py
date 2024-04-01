import database

def check_duplicate():
    qr = "select * from brapci_elastic.dataset order by TITLE, AUTHORS "
    row = database.query(qr)

    last = ''

    for item in row:
        print(item)