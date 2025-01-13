import database

def rule01():
    # Remove os dados da Revista Pesquisa Brasileira em Ciência da Informação e Biblioteconomia (12) que não tem PDF
    qr = "select ID from brapci_elastic.dataset where JOURNAL = 12 and `use` < 0"
    row = database.query(qr)

    print(qr)

    print(row)
