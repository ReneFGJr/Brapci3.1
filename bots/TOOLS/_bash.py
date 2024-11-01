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
ids = [
258122
]

# Converter a lista para um array numpy
ids_array = np.array(ids)
ids_array

for it in ids_array:
    print("=====",it)
    exec(it)
