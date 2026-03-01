from elasticsearch import Elasticsearch


def search_elastic_with_expansion(
        consulta_expandida_array,
        id_list,
        index_name="brapci3.3",
        es_host="http://localhost:9200",
        size=50):

    """
    Consulta ElasticSearch usando:
    - consulta_expandida_array (conceitos + variações)
    - filtro por lista de IDs
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

    # 🔹 Query final
    query_body = {
        "size": size,
        "query": {
            "bool": {
                "must": must_clauses,
                "filter": [
                    {
                        "terms": {
                            "id": id_list
                        }
                    }
                ]
            }
        }
    }

    print(query_body)

    response = es.search(index=index_name, body=query_body)

    return response