import sys
import json
import os
import argparse
import mod_thesa_v2, mod_elasticsearch, mod_projetos


def parse_args(argv):
    parser = argparse.ArgumentParser(add_help=True)
    parser.add_argument("--q", required=True, help="Pergunta da busca")
    parser.add_argument("--p", required=False, default=None, help="ID do projeto")
    return parser.parse_args(argv)


def main():
    try:
        args = parse_args(sys.argv[1:])
    except SystemExit:
        print(json.dumps({
            "status": "error",
            "message": "Parâmetros inválidos. Use --q 'pergunta' [--p projeto]"
        }, ensure_ascii=False))
        sys.exit(1)

    pergunta = args.q
    projeto = args.p

    base_dir = os.path.dirname(os.path.abspath(__file__))
    MM = 25
    if (projeto):
        IDS = mod_projetos.get_ids_by_project(projeto)
        #IDS = [351955, 351956, 351959, 351960, 351961, 351962, 351963, 351964, 351965, 351966]
    else:
        IDS = None

    memory = os.path.join(base_dir, 'data', 'thesa_{}.json'.format(MM))

    if not os.path.exists(memory):
        mod_thesa_v2.getThesa(MM)

    resultado = mod_thesa_v2.rag_query_v2(pergunta, memory)

    #resultado_el = mod_elasticsearch.search_elastic_with_expansion(
    #    consulta_expandida_array=resultado["consulta_expandida_array"],
    #    id_list=IDS
    #)



    resultado_el = mod_elasticsearch.search_elastic_with_expansion(
        consulta_expandida_array=resultado["estrategia_expansao"],
        id_list=IDS
    )

    resultado["ids"] = resultado_el

    print(json.dumps(resultado, ensure_ascii=False))


if __name__ == "__main__":
    main()
