import mod_literal
import mod_class
import mod_concept
import mod_data
import database

table = "brapci.section"

def classificationSection():
    qr = f"select * from brapci_oaipmh.oai_setspec where s_section = 1969"
    row = database.query(qr)

    if row == []:
        print(f"Nenhum section para classificar")
    else:
        for r in row:
            print(f"Classificando {r[0]}")
            classSection(row)
    return 0

def classSection(row):
    print(row)

def getSection(Name):
    qr = f"select sc_rdf from brapci.sections where sc_name = '{Name}'"
    row = database.query(qr)

    if row == []:
        print(f"ERRO DE SECTION {Name}")
    else:
        return row[0][0]
    return 0
