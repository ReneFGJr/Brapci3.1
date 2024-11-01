import database
import io


def directory(id):
    os.makedirs(caminho, exist_ok=True)
def convert():
    qr = "SELECT D2.d_r1, id_n, n_name FROM brapci_rdf.rdf_literal  "
    qr += "JOIN brapci_rdf.rdf_data as D1 ON D1.d_literal = id_n "
    qr += "JOIN brapci_rdf.rdf_data as D2 ON D2.d_r2 = D1.d_r1 "
    qr += "WHERE `n_name` like '_repository/1%' "
    qr += "limit 1 "

    row = database.query(qr)

    print("Convert")
    for line in row:
        print(line)

convert()