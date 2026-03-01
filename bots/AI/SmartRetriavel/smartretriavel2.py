import sys
import json
import os
import mod_thesa_v2

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
    memory = os.path.join(base_dir, 'data', 'thesa_{}.json'.format(MM))

    if not os.path.exists(memory):
        mod_thesa_v2.getThesa(MM)

    resultado = mod_thesa_v2.rag_query(pergunta, memory)

    print(json.dumps(resultado, ensure_ascii=False, indent=2))

if __name__ == "__main__":
    main()
