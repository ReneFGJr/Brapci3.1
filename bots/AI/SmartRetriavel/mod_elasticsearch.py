import json, sys
import warnings
import unicodedata
from elasticsearch import Elasticsearch
from elasticsearch import ElasticsearchWarning

# 🔹 Silencia apenas o warning do Elastic
warnings.filterwarnings("ignore", category=ElasticsearchWarning)

from elasticsearch import Elasticsearch
import json


def normalize_text(text):
    text = str(text).lower()
    text = unicodedata.normalize("NFD", text)
    text = "".join(c for c in text if unicodedata.category(c) != "Mn")
    return text


def search_elastic_with_expansion(consulta_expandida_array,
                                  id_list=None,
                                  index_name="brapci3.3",
                                  es_host="http://143.54.112.91:9200",
                                  size=500):

    es = Elasticsearch(es_host)

    must_clauses = []

    for concept_block in consulta_expandida_array:

        variations = concept_block["variations"]
        should_terms = []

        for term in variations:
            term = normalize_text(term)
            should_terms.append({
                "multi_match": {
                    "query": term,
                    "fields": ["title^6", "keywords^3", "abstract"],
                    "type": "phrase"
                }
            })

        # OR dentro do conceito
        must_clauses.append(
            {"bool": {
                "should": should_terms,
                "minimum_should_match": 1
            }})

    # ✅ AND entre conceitos
    bool_query = {"must": must_clauses}

    # filtro opcional
    if id_list:
        bool_query["filter"] = [{"terms": {"id": id_list}}]

    query_body = {
        "size": size,
        "_source": ["id"],
        "query": {
            "bool": bool_query
        }
    }

    response = es.search(index=index_name, body=query_body)

    ids = [hit["_source"]["id"] for hit in response["hits"]["hits"]]

    return ids

def search_elastic_with_expansion_old(
        consulta_expandida_array,
        id_list=None,
        index_name="brapci3.3",
        es_host="http://143.54.112.91:9200",
        size=500):

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

    print("Consulta expandida para Elastic:", json.dumps(bool_query, ensure_ascii=False))
    sys.exit()

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

    return ids
