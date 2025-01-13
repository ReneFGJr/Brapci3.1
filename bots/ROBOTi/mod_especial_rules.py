import database
import mod_concept
import sys

def rule01():
    # Remove os dados da Revista Pesquisa Brasileira em Ciência da Informação e Biblioteconomia (12) que não tem PDF
    qr = "select ID from brapci_elastic.dataset where JOURNAL = 12 and `PDF` < 0"
    row = database.query(qr)

    for line in row:
        print(line)
        ID = line[0]
        print("Removido trabalho ",ID," da Revista RPBCIB")
        mod_concept.remove(ID)
        sys.exit()
