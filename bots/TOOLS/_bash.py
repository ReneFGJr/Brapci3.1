import subprocess

def exec(id):
    id = str(id)
    # Executando outro script com argumentos
    print("Processing",id)
    rst = subprocess.run(["python3", "ai.py","section", id])
    print(rst)
    #rst = subprocess.run(["python3", "ai.py","keywords", id])
    #print(rst)


import numpy as np

# Lista de IDs fornecida
# Caminho do arquivo
caminho_arquivo = 'dados.txt'

# Leitura do arquivo e armazenamento dos dados em uma lista
with open(caminho_arquivo, 'r') as file:
    dados = [int(linha.strip()) for linha in file]

# Converter a lista para um array numpy
ids_array = np.array(dados)

for it in ids_array:
    print("=====",it)
    if (it != ''):
        exec(it)
