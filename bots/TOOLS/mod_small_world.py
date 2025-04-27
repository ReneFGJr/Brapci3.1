import database
import os
import itertools


def proccess():
    print("     Small World")

    qr = "select AUTHORS from brapci_elastic.dataset"
    rows = database.query(qr)

    # 1. Extrair e normalizar nomes e manter lista por linha para criar arestas
    authors_set = set()
    rows_authors = []  # lista de listas: cada sublista são autores daquele registro
    for (cell,) in rows:
        cell = cell.replace('(Org.)', '')
        if not cell or not cell.strip():
            continue
        names = [name.strip() for name in cell.split(';') if name.strip()]
        if not names:
            continue
        rows_authors.append(names)
        authors_set.update(names)

    # Ordenar autores para índice consistente
    authors_list = sorted(authors_set)
    index = {author: i+1 for i, author in enumerate(authors_list)}

    # 2. Construir vértices Pajek
    lines = []
    lines.append(f"*Vertices {len(authors_list)}")
    for author, idx in index.items():
        safe_label = author.replace('"', '\\"')
        lines.append(f'{idx} "{safe_label}"')

    # 3. Construir as arestas de coautoria
    edges = set()
    for authors in rows_authors:
        # para cada par de coautores, criar uma aresta não direcionada
        for a, b in itertools.combinations(authors, 2):
            i, j = index[a], index[b]
            # ordena (i,j) para evitar duplicata (i,j) e (j,i)
            edge = tuple(sorted((i, j)))
            edges.add(edge)

    # 4. Adicionar seção de arestas
    lines.append("*Edges")
    for i, j in sorted(edges):
        # peso 1 para cada coautoria única (você pode somar múltiplas colaborações se quiser)
        lines.append(f"{i} {j} 1")

    # 5. Gravar em disco
    output_file = "authors_coauthorship_pajek.net"
    with open(output_file, "w", encoding="utf-8") as f:
        f.write("\n".join(lines))

    print(f"Arquivo Pajek gerado com vértices e arestas: {output_file}")