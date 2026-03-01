from elasticsearch import Elasticsearch
import json
import sys


def search_elastic_with_expansion(
        consulta_expandida_array,
        id_list=None,  # agora opcional
        index_name="brapci3.3",
        es_host="http://localhost:9200",
        size=50):

    """
    Consulta ElasticSearch usando:
    - consulta_expandida_array (conceitos + variações)
    - filtro opcional por lista de IDs
    """

    es = Elasticsearch(es_host)

    must_clauses = []

    # 🔹 Para cada conceito
    for concept_block in consulta_expandida_array:

        variations = concept_block["variations"]

        should_terms = []

        for term in variations:
            should_terms.append({
                "multi_match": {
                    "query": term,
                    "fields": [
                        "title^3",
                        "keywords^2",
                        "abstract"
                    ],
                    "type": "phrase"
                }
            })

        must_clauses.append({
            "bool": {
                "should": should_terms,
                "minimum_should_match": 1
            }
        })

    # 🔹 Estrutura base
    bool_query = {
        "should": must_clauses
    }

    # 🔹 Só adiciona filtro se id_list existir
    if id_list:
        bool_query["filter"] = [
            {
                "terms": {
                    "id": id_list
                }
            }
        ]

    query_body = {
        "size": size,
        "query": {
            "bool": bool_query
        }
    }

    print(json.dumps(query_body, indent=2, ensure_ascii=False))

    response = es.search(index=index_name, body=query_body)

    return response