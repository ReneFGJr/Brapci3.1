import mod_class
import database

def check_type():
    print("Checando tipo de publicação e o trabalho")
    qr = "select ID, CLASS, jnl_name "
    qr += " from brapci_elastic.dataset "
    qr += " inner join brapci.source_source on JOURNAL = id_jnl "
    qr += " where CLASS <> 'Proceeding' "
    qr += " and jnl_collection = 'EV' "

    row = database.query(qr)

    classN = mod_class.getClass('Proceeding')
    for item in row:
        print(item)
        ID = item[0]

        qu = f"update brapci_rdf.rdf_concept set cc_class = {classN} where id_cc = {ID} "
        print(qu)

def check_duplicate():
    qr = "select JOURNAL, TITLE, AUTHORS, ID, YEAR from brapci_elastic.dataset "
    qr += " where `use` = 0 "
    qr += "order by JOURNAL, TITLE, AUTHORS, YEAR, PDF desc, ID "
    row = database.query(qr)

    last = ''
    lastID = ''
    tot = 0

    for item in row:
        name = str(item[0]) + ' | ' + item[1]+' | ' + item[2]+' | ' + str(item[4])
        ID = item[3]

        if (name == last):
            print(ID,lastID,name)
            tot = tot + 1
            qu = "update brapci_elastic.dataset "
            qu += f" set `use` = {lastID} "
            qu += f" where ID = {ID} "
            print("===>",qu)
            database.update(qu)

        last = name
        lastID = ID
    print("Total",tot)