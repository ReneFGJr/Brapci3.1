import mod_literal
import mod_class
import mod_concept
import mod_data
import database

table = "brapci.section"

def getSection(Name):
    qr = f"select sc_rdf from brapci.sections where sc_name = '{Name}'"
    row = database.query(qr)

    if row == []:
        print(f"ERRO DE SECTION {Name}")
    else:
        return row[0][0]
    return 0
