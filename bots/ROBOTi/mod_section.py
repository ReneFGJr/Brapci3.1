import mod_literal
import mod_class
import mod_concept
import mod_data
import database
import sys

table = "brapci.section"

def classificationSection():
    qr = f"select * from brapci_oaipmh.oai_setspec where s_section = 1969 and id_s <> 1969"
    row = database.query(qr)

    if row == []:
        print(f"Nenhum section para classificar")
    else:
        for r in row:
            print(f"Classificando {r[3]}")
            classSection(r)
    return 0

def classSection(row):
    print(row)
    ids = 1

    updateSection(row[0], ids)
    sys.exit()

def updateSection(id_section, id_class):
    qu = f"update brapci_oaipmh.oai_setspec set s_section = {id_class} where id_s = {id_section}"
    database.update(qu)

def getSection(Name):
    qr = f"select sc_rdf from brapci.sections where sc_name = '{Name}'"
    row = database.query(qr)

    if row == []:
        print(f"ERRO DE SECTION {Name}")
    else:
        return row[0][0]
    return 0
