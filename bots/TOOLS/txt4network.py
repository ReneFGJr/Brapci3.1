import json
import networkx as nx
from itertools import combinations
from collections import Counter


def calcular_rede_coautoria(text: str) -> dict:
    """
    Calcula indicadores de uma rede de coautoria.

    Entrada
    -------
    text : str
        Cada linha representa um trabalho/publicação.
        Os autores devem estar separados por ponto e vírgula (;).

        Exemplo:
        Ronaldo Ferreira de Araujo; Marcelo Alves
        João de Melo Maricato; Dalton Lopes Martins
        Ronaldo Ferreira de Araujo; Marcelo Alves; João de Melo Maricato

    Retorno
    -------
    dict
        Estrutura pronta para conversão em JSON contendo:
        - indicadores globais da rede;
        - tabela de autores;
        - tabela de relações/coautorias;
        - comunidades.
    """

    # ==========================================================
    # 1. PROCESSAR AS PUBLICAÇÕES
    # ==========================================================

    publicacoes = []

    for linha in text.splitlines():
        linha = linha.strip()

        if not linha:
            continue

        autores = [
            autor.strip()
            for autor in linha.split(";")
            if autor.strip()
        ]

        # Remove duplicação do mesmo autor dentro do trabalho
        autores = list(dict.fromkeys(autores))

        if autores:
            publicacoes.append(autores)

    # ==========================================================
    # 2. CRIAR GRAFO
    # ==========================================================

    G = nx.Graph()

    producao = Counter()

    for autores in publicacoes:

        # contabiliza produção
        for autor in autores:
            producao[autor] += 1
            G.add_node(autor)

        # cria todas as combinações de coautoria
        for autor1, autor2 in combinations(autores, 2):

            if G.has_edge(autor1, autor2):
                G[autor1][autor2]["weight"] += 1
            else:
                G.add_edge(
                    autor1,
                    autor2,
                    weight=1
                )

    # ==========================================================
    # 3. INDICADORES DE CENTRALIDADE
    # ==========================================================

    degree = dict(G.degree())

    weighted_degree = dict(
        G.degree(weight="weight")
    )

    # Degree normalizado
    degree_centrality = nx.degree_centrality(G)

    # Betweenness
    betweenness = nx.betweenness_centrality(
        G,
        normalized=True
    )

    # Closeness
    closeness = nx.closeness_centrality(G)

    # Harmonic centrality
    harmonic = nx.harmonic_centrality(G)

    # ==========================================================
    # 4. EIGENVECTOR
    # ==========================================================

    try:
        eigenvector = nx.eigenvector_centrality(
            G,
            max_iter=2000,
            weight="weight"
        )
    except nx.PowerIterationFailedConvergence:
        eigenvector = {
            node: 0.0
            for node in G.nodes()
        }

    # ==========================================================
    # 5. PAGERANK
    # ==========================================================

    if G.number_of_nodes() > 0:
        pagerank = nx.pagerank(
            G,
            weight="weight"
        )
    else:
        pagerank = {}

    # ==========================================================
    # 6. CLUSTERING
    # ==========================================================

    clustering = nx.clustering(
        G,
        weight="weight"
    )

    # ==========================================================
    # 7. COMUNIDADES
    # ==========================================================

    comunidade_autor = {}
    comunidades_json = []

    if G.number_of_edges() > 0:

        comunidades = list(
            nx.community.greedy_modularity_communities(
                G,
                weight="weight"
            )
        )

        for numero, comunidade in enumerate(
            comunidades,
            start=1
        ):

            autores_comunidade = sorted(comunidade)

            comunidades_json.append({
                "id": numero,
                "total_autores": len(autores_comunidade),
                "autores": autores_comunidade
            })

            for autor in comunidade:
                comunidade_autor[autor] = numero

    else:

        for numero, autor in enumerate(
            G.nodes(),
            start=1
        ):
            comunidade_autor[autor] = numero

            comunidades_json.append({
                "id": numero,
                "total_autores": 1,
                "autores": [autor]
            })

    # ==========================================================
    # 8. COMPONENTES
    # ==========================================================

    componentes = list(
        nx.connected_components(G)
    )

    numero_componentes = len(componentes)

    if componentes:
        maior_componente = max(
            componentes,
            key=len
        )

        tamanho_maior_componente = len(
            maior_componente
        )
    else:
        maior_componente = set()
        tamanho_maior_componente = 0

    # ==========================================================
    # 9. INDICADORES GLOBAIS
    # ==========================================================

    numero_nos = G.number_of_nodes()
    numero_arestas = G.number_of_edges()

    densidade = (
        nx.density(G)
        if numero_nos > 1
        else 0
    )

    grau_medio = (
        sum(dict(G.degree()).values()) / numero_nos
        if numero_nos > 0
        else 0
    )

    weighted_degree_medio = (
        sum(weighted_degree.values()) / numero_nos
        if numero_nos > 0
        else 0
    )

    clustering_medio = (
        nx.average_clustering(
            G,
            weight="weight"
        )
        if numero_nos > 0
        else 0
    )

    percentual_componente_gigante = (
        tamanho_maior_componente / numero_nos * 100
        if numero_nos > 0
        else 0
    )

    # ==========================================================
    # 10. MODULARIDADE
    # ==========================================================

    if numero_arestas > 0 and comunidades_json:

        comunidades_sets = [
            set(c["autores"])
            for c in comunidades_json
        ]

        modularidade = nx.community.modularity(
            G,
            comunidades_sets,
            weight="weight"
        )

    else:
        modularidade = 0

    # ==========================================================
    # 11. TABELA DOS AUTORES
    # ==========================================================

    tabela_autores = []

    for autor in G.nodes():

        tabela_autores.append({

            "autor": autor,

            # produção
            "documentos": int(
                producao[autor]
            ),

            # colaboração
            "coautores": int(
                degree.get(autor, 0)
            ),

            "degree": int(
                degree.get(autor, 0)
            ),

            "weighted_degree": float(
                weighted_degree.get(autor, 0)
            ),

            # centralidades
            "degree_centrality": float(
                degree_centrality.get(autor, 0)
            ),

            "betweenness": float(
                betweenness.get(autor, 0)
            ),

            "closeness": float(
                closeness.get(autor, 0)
            ),

            "harmonic": float(
                harmonic.get(autor, 0)
            ),

            "eigenvector": float(
                eigenvector.get(autor, 0)
            ),

            "pagerank": float(
                pagerank.get(autor, 0)
            ),

            "clustering": float(
                clustering.get(autor, 0)
            ),

            "comunidade": int(
                comunidade_autor.get(autor, 0)
            )
        })

    # Ordena inicialmente pelos autores com maior degree
    tabela_autores.sort(
        key=lambda x: (
            x["degree"],
            x["weighted_degree"],
            x["documentos"]
        ),
        reverse=True
    )

    # Ranking
    for posicao, autor in enumerate(
        tabela_autores,
        start=1
    ):
        autor["ranking_degree"] = posicao

    # ==========================================================
    # 12. TABELA DE ARESTAS
    # ==========================================================

    relacoes = []

    for autor1, autor2, dados in G.edges(data=True):

        relacoes.append({
            "autor1": autor1,
            "autor2": autor2,
            "peso": int(
                dados.get("weight", 1)
            )
        })

    relacoes.sort(
        key=lambda x: x["peso"],
        reverse=True
    )

    # ==========================================================
    # 13. RESULTADO
    # ==========================================================

    resultado = {

        "rede": {

            "total_documentos":
                len(publicacoes),

            "total_autores":
                numero_nos,

            "total_relacoes":
                numero_arestas,

            "densidade":
                float(densidade),

            "grau_medio":
                float(grau_medio),

            "weighted_degree_medio":
                float(weighted_degree_medio),

            "clustering_medio":
                float(clustering_medio),

            "numero_componentes":
                numero_componentes,

            "tamanho_componente_gigante":
                tamanho_maior_componente,

            "percentual_componente_gigante":
                float(
                    percentual_componente_gigante
                ),

            "modularidade":
                float(modularidade)
        },

        "autores": tabela_autores,

        "relacoes": relacoes,

        "comunidades": comunidades_json
    }

    return resultado