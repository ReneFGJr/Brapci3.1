import mod_literal
import mod_class
import mod_concept
import mod_data
import database

table = "brapci.section"

def getSection(Name):
    qr = f"select * from brapci.sections where sc_name = '{Name}'"
    row = database.query(qr)
    print(row)
    quit()
    return 0
