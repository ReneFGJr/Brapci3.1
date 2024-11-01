import database
import io

def convert():
    qr = "SELECT * FROM brapci_rdf.rdf_literal "
    qr += "JOIN brapci_rdf.rdf_data ON d_literal = id_n "
    qr += "WHERE `n_name` like '_repository/1%' "
    qr += "limit 1 "

    row = database.query(qr)
    print("Convert")
    print(row)

    print(qr)

convert()