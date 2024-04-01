import database

def check_duplicate():
    qr = "select JOURNAL, TITLE, AUTHORS, ID from brapci_elastic.dataset order by JOURNAL, TITLE, AUTHORS, ID "
    row = database.query(qr)

    last = ''
    lastID = ''
    tot = 0

    for item in row:
        name = item[1]+item[2]
        ID = item[3]

        if (name == last):
            print(ID,lastID,name)
            tot = tot + 1
        last = name
        lastID = ID
    print("Total",tot)