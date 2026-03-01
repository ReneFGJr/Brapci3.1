import json
import warnings
from elasticsearch import Elasticsearch
from elasticsearch import ElasticsearchWarning

# 🔹 Silencia apenas o warning do Elastic
warnings.filterwarnings("ignore", category=ElasticsearchWarning)


def search_elastic_with_expansion(
        consulta_expandida_array,
        id_list=None,
        index_name="brapci3.3",
        es_host="http://143.54.112.91:9200",
        size=50):

    # ✅ Cliente correto
    es = Elasticsearch(es_host)

    must_clauses = []

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

    bool_query = {
        "should": must_clauses,
        "minimum_should_match": 1
    }

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
        "_source": ["id"],
        "query": {
            "bool": bool_query
        }
    }

    response = es.search(index=index_name, body=query_body)

    ids = [hit["_source"]["id"] for hit in response["hits"]["hits"]]

    return {
        "result": ids
    }