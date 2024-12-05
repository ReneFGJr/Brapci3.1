import pandas as pd
import networkx as nx
from unidecode import unidecode
import sys

def criar_matriz_coautores(arquivo_entrada, arquivo_saida):
    G = nx.Graph()

    # Ler o arquivo de entrada
    with open(arquivo_entrada, 'r', encoding='utf-8') as f:
        for linha in f:
            # Processar autores na linha
            autores = [abreviar_nome(autor.strip()) for autor in linha.split(";") if autor.strip()]

            # Criar as conexões no grafo
            for i in range(len(autores)):
                for j in range(i + 1, len(autores)):
                    autor1 = autores[i]
                    autor2 = autores[j]

                    if G.has_edge(autor1, autor2):
                        # Incrementar o peso
                        G[autor1][autor2]['weight'] += 1
                    else:
                        # Adicionar uma aresta com peso inicial
                        G.add_edge(autor1, autor2, weight=1)

    # Criar a matriz de adjacência
    matriz = nx.to_pandas_adjacency(G, weight='weight').fillna(0)

    # Salvar a matriz como arquivo Excel
    print(arquivo_saida)
    matriz.to_excel(arquivo_saida, sheet_name='Coautoria')

    print(f"Matriz de coautoria salva em: {arquivo_saida}")

def abreviar_nome(nome_completo):
    # Normalizar o nome completo
    nome_completo = nome_completo.lower()
    nome_completo = unidecode(nome_completo)
    nome_completo = nome_completo.replace(" de ", " ")
    nome_completo = nome_completo.replace(" do ", " ")
    nome_completo = nome_completo.replace(" dos ", " ")
    nome_completo = nome_completo.replace(" das ", " ")
    nome_completo = nome_completo.replace(" da ", " ")
    nome_completo = nome_completo.replace(" e ", " ")
    nome_completo = nome_completo.replace(" em ", " ")

    # Processar as partes do nome
    partes = nome_completo.split()
    sobrenome = partes[-1]
    iniciais = ''.join([parte[0].upper() + '.' for parte in partes[:-1]])

    return f"{sobrenome.capitalize()}, {iniciais}"

if __name__ == "__main__":
    # Verificar os argumentos fornecidos
    if len(sys.argv) != 2:
        print("Uso: python script.py <arquivo_entrada> <arquivo_saida>")
        sys.exit(1)

    # Obter caminhos dos arquivos
    arquivo_entrada = sys.argv[1]
    arquivo_saida = sys.argv[2]
    if arquivo_saida == '':
        arquivo_saida = arquivo_entrada.replace('.txt','.xlsx')

    # Criar a matriz de coautoria
    criar_matriz_coautores(arquivo_entrada, arquivo_saida)
