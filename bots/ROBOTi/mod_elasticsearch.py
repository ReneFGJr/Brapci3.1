import database

def dataset_news():
    qr = "select * from brapci_elastic.dataset where new = 1"
    row = database.query(qr)

    for ln in row:
        print(ln)