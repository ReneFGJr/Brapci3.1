import database
import mod_concept
import mod_elasticsearch
import sys

def rule01():
    limit = 1000
    # Remove os dados da Revista Pesquisa Brasileira em Ciência da Informação e Biblioteconomia (12) que não tem PDF
    qr = f"select ID,id_ds from brapci_elastic.dataset where JOURNAL = 12 and ((`PDF` < 0) or (`use` = -99)) limit {limit}"
    row = database.query(qr)

    for line in row:
        ID = line[0]
        IDe = line[1]
        print("Removido trabalho ",ID," da Revista RPBCIB")
        mod_concept.remove(ID)
        mod_elasticsearch.remove(IDe)
