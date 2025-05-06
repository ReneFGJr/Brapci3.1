import networkx as nx
import pandas as pd
from itertools import combinations
import database
import os
import re
import sys
from collections import defaultdict
import unicodedata
import json

def load_graph(path='../../.tmp/brapci.net'):
    """
    Carrega o grafo no formato Pajek a partir do arquivo especificado.
    Retorna um objeto NetworkX Graph ou DiGraph.
    """
    try:
        G = nx.read_pajek(path)
    except FileNotFoundError:
        print(f"Erro: arquivo '{path}' não encontrado.")
        exit(1)

    # Converte em grafo padrão: se for MultiGraph/MultiDiGraph, pega um simples
    if isinstance(G, (nx.MultiGraph, nx.MultiDiGraph)):
        G = nx.Graph(G) if not G.is_directed() else nx.DiGraph(G)
    return G

def choose_nodes(graph):
    """
    Lista os nós e solicita ao usuário escolher origem e destino.
    """
    nodes = list(graph.nodes())
    print("Nós disponíveis:")
    for n in nodes:
        print(f"  - {n}")
    source = input("Digite o nó de origem: ").strip()
    target = input("Digite o nó de destino: ").strip()
    if source not in graph or target not in graph:
        print("Origem ou destino inválido(s). Verifique a lista de nós.")
        exit(1)
    print("-----",source,target)
    return source, target

def compute_shortest_path(graph, source, target):
    """
    Tenta usar Dijkstra (ponderado). Se falhar por falta de pesos, usa busca simples.
    """
    try:
        # Assume que há atributo 'weight' nas arestas
        path = nx.dijkstra_path(graph, source, target, weight='weight')
        dist = nx.dijkstra_path_length(graph, source, target, weight='weight')
    except (nx.NetworkXNoPath, nx.NetworkXError):
        try:
            path = nx.shortest_path(graph, source, target)
            dist = len(path) - 1
        except nx.NetworkXNoPath:
            print(f"Não existe caminho entre {source} e {target}.")
            exit(0)
    return path, dist

def display_result(path, dist):
    """
    Exibe o caminho em DataFrame e imprime distância.
    """
    df = pd.DataFrame({
        'Passo': list(range(1, len(path) + 1)),
        'Nó': path
    })
    print("\nCaminho mínimo encontrado:")
    print(df.to_string(index=False))
    print(f"\nDistância mínima de '{path[0]}' até '{path[-1]}': {dist}")


def main(source, target):
    if len(sys.argv) != 2:
        print("Uso: python dijkstra.py <nó de destino>")
        exit(1)
    graph = load_graph('../../.tmp/brapci.net')
    #source, target = choose_nodes(graph)
    path, dist = compute_shortest_path(graph, source, target)
    display_result(path, dist)

#*********************************** Gera o de grafo ***************************
def normalize(name: str) -> str:
    """
    Limpa espaços em excesso e uniformiza capitalização.
    Ex.: " joão silva " -> "João Silva"
    """
    name = name.replace('(Org.)','')
    return ' '.join(name.strip().title().split())

def fetch_author_lists():
    """Busca todos os campos AUTHORS e retorna lista de listas de autores."""
    qr = (f"SELECT AUTHORS FROM brapci_elastic.dataset where AUTHORS <> ''")
    rows = database.query(qr)

    author_lists = []
    for (cell,) in rows:
        if not cell:
            continue
        # Divide por ';' e normaliza cada nome
        authors = [normalize(a) for a in cell.split(';') if a.strip()]
        # Remove duplicatas dentro do mesmo paper
        unique = list(dict.fromkeys(authors))
        if len(unique) > 1:
            author_lists.append(unique)
    return author_lists

def build_vertices_edges(author_lists):
    """Gera o mapeamento de vértices e a lista de arestas (índices)."""
    # Constrói o dicionário de todos os autores únicos
    all_authors = sorted({a for lst in author_lists for a in lst})
    # Mapeia autor -> índice (1‑based para Pajek)
    idx = {author: i+1 for i, author in enumerate(all_authors)}

    edges = []
    for lst in author_lists:
        # Gera todas as combinações de pares sem repetição
        for a, b in combinations(lst, 2):
            edges.append((idx[a], idx[b]))
    return all_authors, edges

def write_pajek(all_authors, edges):
    """Escreve o arquivo .net no formato Pajek."""
    filename = '../../.tmp/brapci.net'
    with open(filename, 'w', encoding='utf-8') as f:
        # Vértices
        f.write(f"*Vertices {len(all_authors)}\n")
        for i, author in enumerate(all_authors, start=1):
            # Aspas para nomes com espaços
            f.write(f'{i} "{author}"\n')

        # Arestas (não-direcionadas)
        f.write("*Edges\n")
        for u, v in edges:
            f.write(f"{u} {v} 1\n")  # peso=1 para cada coautoria

    print(f"Arquivo Pajek gerado: {filename}")

#***************************** Extrai autores
def extract_authors(net_path: str, output_path: str):
    """
    Lê um arquivo Pajek .net, extrai os nomes da seção *Vertices
    e salva cada nome em uma linha no arquivo de saída.
    """
    vertices_section = False
    author_pattern = re.compile(r'^\s*\d+\s+"(.+)"')  # captura o texto entre aspas

    with open(net_path, 'r', encoding='utf-8') as fin, \
         open(output_path, 'w', encoding='utf-8') as fout:

        for line in fin:
            # Início da seção de vértices
            if line.strip().lower().startswith('*vertices'):
                vertices_section = True
                continue

            # Quando encontrar outra seção, encerra a leitura de vértices
            if vertices_section and line.strip().startswith('*'):
                break

            if vertices_section:
                match = author_pattern.match(line)
                if match:
                    name = match.group(1).strip()
                    fout.write(name + '\n')

    print(f"✔ {output_path} gerado com sucesso.")
############################################## Indice reverso
def normalize_token(tok: str) -> str:
    """
    Normaliza um token: remove acentos, pontuação e passa para minúsculas.
    """
    tok = unicodedata.normalize('NFKD', tok)
    tok = ''.join([c for c in tok if not unicodedata.combining(c)])
    tok = re.sub(r'[^a-zA-Z0-9]', '', tok)
    return tok.lower()

def build_inverted_index(authors_path: str) -> tuple[dict, list]:
    """
    Lê o arquivo de autores e retorna um dicionário:
      token -> list de índices de autores que o contêm,
    além da lista de autores.
    """
    index = defaultdict(set)
    authors = []

    with open(authors_path, 'r', encoding='utf-8') as f:
        for i, line in enumerate(f):
            name = line.strip()
            if not name:
                continue
            authors.append(name)
            for raw_tok in name.split():
                tok = normalize_token(raw_tok)
                if tok:
                    index[tok].add(i)

    # Converte sets para listas para serialização
    serializable_index = {tok: sorted(list(idxs)) for tok, idxs in index.items()}
    return serializable_index, authors

def save_index_to_file(index: dict, authors: list, output_path: str):
    """
    Salva o índice invertido e a lista de autores em um JSON para uso em API.
    Estrutura:
    {
      "authors": ["Autor A", "Autor B", ...],
      "index": { "silva": [0, 2, ...], ... }
    }
    """
    data = {
        'authors': authors,
        'index': index
    }
    with open(output_path, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False)
    print(f"✔ Arquivo de índice salvo em: {output_path}")

def query_index(json_path: str, query: str) -> list:
    """
    Carrega arquivo JSON de índice invertido e busca autores cujos
    tokens contenham todos os termos da 'query' como substrings.

    Parâmetros:
      json_path: caminho do arquivo JSON gerado
      query: string de busca (múltiplos termos)

    Retorna:
      lista de nomes de autores encontrados
    """
    # Carrega dados
    with open(json_path, 'r', encoding='utf-8') as f:
        data = json.load(f)
    authors = data.get('authors', [])
    index = data.get('index', {})

    # Normaliza e tokeniza a query em termos simplificados
    tokens = [normalize_token(tok) for tok in query.split() if tok.strip()]
    if not tokens:
        return []

    # Para cada token, busca chaves de index que contenham o token
    token_matches = []
    for tok in tokens:
        matched_idxs = set()
        for key, idxs in index.items():
            if tok in key:  # substring match
                matched_idxs |= set(idxs)
        token_matches.append(matched_idxs)

    # Interseção de todos resultados (AND)
    if not token_matches:
        return []
    result_idxs = token_matches[0]
    for idxs in token_matches[1:]:
        result_idxs &= idxs

    # Retorna nomes conforme índices ordenados
    return [authors[i] for i in sorted(result_idxs)]


###################################
def bsmain():
    filename = '../../.tmp/brapci.json'
    #################### Consulta
    query = ' '.join(sys.argv[2:])
    results = query_index(filename, query)
    if results:
        print(f"Encontrei {len(results)} autor(es):")
        for name in results:
            print(f"  - {name}")
    else:
        print("Nenhum autor encontrado para essa consulta.")


if __name__ == "__main__":
    filename = '../../.tmp/brapci.net'
    if not os.path.exists(filename):
        author_lists = fetch_author_lists()
        all_authors, edges = build_vertices_edges(author_lists)
        write_pajek(all_authors, edges)
        extract_authors(filename, '../../.tmp/authors.txt')
        index, authors = build_inverted_index(filename)
        save_index_to_file(index, authors, filename.replace('.net', '.json'))
    main()
