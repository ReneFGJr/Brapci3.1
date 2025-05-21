import sys
import networkx as nx
from unidecode import unidecode


def criar_grafo_autores(arquivo_entrada, arquivo_saida):
    G = nx.Graph()

    # Abrir o arquivo de entrada
    with open(arquivo_entrada, 'r', encoding='utf-8') as f:
        for linha in f:
            autores = [
                autor.strip() for autor in linha.split(";") if autor.strip()
            ]
            for i in range(len(autores)):
                for j in range(i + 1, len(autores)):
                    autor1 = autores[i]
                    autor2 = autores[j]

                    if G.has_edge(autor1, autor2):
                        G[autor1][autor2]['weight'] += 1
                    else:
                        G.add_edge(autor1, autor2, weight=1)

    # Lista de nós ordenada para garantir consistência de índices
    nodes_list = list(G.nodes())
    posX = 0
    posY = 0

    # Criar o arquivo .net
    with open(arquivo_saida, 'w', encoding='utf-8') as f_out:
        f_out.write('#term4net\n')
        f_out.write("*Vertices {}\n".format(len(nodes_list)))
        for i, node in enumerate(nodes_list, start=1):
            grau = G.degree(node)
            # Definir tamanho do nó baseado no grau
            fator = "2.0000" if grau > 5 else "1.0000"
            posX += 10
            if (posX % 100) == 0:
                posY += 10
                posX = 0
            f_out.write(
                f'{i} "{node}" {posX} {fator} {posY} \n'
            )

        # Escrever as arestas
        f_out.write("*Edges\n")
        for autor1, autor2, data in G.edges(data=True):
            node1_index = nodes_list.index(autor1) + 1
            node2_index = nodes_list.index(autor2) + 1
            f_out.write(f'{node1_index} {node2_index} {data["weight"]}\n')

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: python3 term4net.py <input_file>")
        sys.exit(1)

    input_file = sys.argv[1]
    criar_grafo_autores(input_file, input_file.replace('.txt', '.net'))
