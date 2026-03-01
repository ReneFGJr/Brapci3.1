import sys
import json
import os
import mod_thesa_v2, mod_elasticsearch

def main():
    if len(sys.argv) < 2:
        print(json.dumps({
            "status": "error",
            "message": "Pergunta não informada"
        }, ensure_ascii=False))
        sys.exit(1)

    # Tudo depois do nome do script vira a pergunta
    pergunta = " ".join(sys.argv[1:])

    base_dir = os.path.dirname(os.path.abspath(__file__))
    MM = 25
    #IDS = [351955, 351956, 351959, 351960, 351961, 351962, 351963, 351964, 351965, 351966]
    IDS = None
    memory = os.path.join(base_dir, 'data', 'thesa_{}.json'.format(MM))

    if not os.path.exists(memory):
        mod_thesa_v2.getThesa(MM)

    resultado = mod_thesa_v2.rag_query_v2(pergunta, memory)

    resultado_el = mod_elasticsearch.search_elastic_with_expansion(
        consulta_expandida_array=resultado["consulta_expandida_array"],
        id_list=IDS
    )

    resultado["ids"] = resultado_el

    print(json.dumps(resultado, ensure_ascii=False))


if __name__ == "__main__":
    main()
