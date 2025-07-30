import sys
import networkx as nx
from pathlib import Path
from unidecode import unidecode

def criar_grafo_autores(arquivo_entrada, arquivo_saida):
    G = nx.Graph()

    # Abrir o arquivo de entrada
    with open(arquivo_entrada, 'r', encoding='utf-8') as f:
        for linha in f:
            # Dividir os autores na linha, removendo nomes vazios
            autores = [abreviar_nome(autor.strip()) for autor in linha.split(";") if autor.strip()]

            # Adicionar arestas entre todos os pares de autores válidos
            for i in range(len(autores)):
                for j in range(i + 1, len(autores)):
                    autor1 = autores[i]
                    autor2 = autores[j]

                    if G.has_edge(autor1, autor2):
                        # Se já existe uma aresta, incrementar o peso
                        G[autor1][autor2]['weight'] += 1
                    else:
                        # Adicionar a aresta com peso 1
                        G.add_edge(autor1, autor2, weight=1)

    # Criar o arquivo .net para salvar o grafo
    with open(arquivo_saida, 'w', encoding='utf-8') as f_out:
        #f.write('#txt4net\n')
        # Escrever os nós
        f_out.write("*Vertices {}\n".format(len(G.nodes)))
        for i, node in enumerate(G.nodes(), start=1):
            f_out.write('{} "{}"\n'.format(i, node))

        # Escrever as arestas com pesos
        f_out.write("*Edges\n")
        for autor1, autor2, data in G.edges(data=True):
            node1_index = list(G.nodes()).index(autor1) + 1
            node2_index = list(G.nodes()).index(autor2) + 1
            f_out.write('{} {} {}\n'.format(node1_index, node2_index, data['weight']))

def abreviar_nome(nome_completo):
    # Divide o nome completo em partes
    nome_completo = nome_completo.replace(' de ',' ')
    nome_completo = nome_completo.replace(' do ',' ')
    nome_completo = nome_completo.replace(' dos ',' ')
    nome_completo = nome_completo.replace(' das ',' ')
    nome_completo = nome_completo.replace(' da ',' ')
    nome_completo = nome_completo.replace(' e ',' ')
    nome_completo = nome_completo.replace(' em ',' ')

    nome_completo = unidecode(nome_completo)
    nome_completo = nome_completo.replace(' Junior','_JR')
    nome_completo = nome_completo.replace(' Neto','_NETO')
    nome_completo = nome_completo.replace(' Netto','_NETTO')
    nome_completo = nome_completo.replace(' Sobrinho','_SOBRINHO')
    nome_completo = nome_completo.replace(' Filho','_FILHO')
    nome_completo = nome_completo.replace(' JR.','_JR')
    nome_completo = nome_completo.replace(' JR','_JR')

    partes = nome_completo.split()

    # O sobrenome será a última parte
    sobrenome = partes[-1]

    # As iniciais serão as primeiras letras de todas as outras partes do nome
    iniciais = ''.join([parte[0] + '.' for parte in partes[:-1]])

    # Retorna no formato "Sobrenome, Iniciais"
    return f"{sobrenome}, {iniciais}"

if __name__ == "__main__":
    # Ensure the correct number of arguments is provided
    if len(sys.argv) < 2:
        print("Usage: python3 txt4net.py <input_file> <output_file>")
        print(sys.argv)
        sys.exit(1)

    # Parse the input and output file paths
    input_file = sys.argv[1]
    print(f"Input file: {input_file}")
    output_file = input_file + '.net'
    print(f"Output file: {output_file}")

    if not Path(input_file).exists():
        print(f"File {input_file} does not exist.")
        sys.exit(1)

    # Create the .net file
    criar_grafo_autores(input_file, output_file)

    print({output_file})
