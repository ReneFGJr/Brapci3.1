import sys
import json
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

    resultado = mod_thesa_v2.rag_query(pergunta, 'thesa_6.json')

    print(json.dumps(resultado, ensure_ascii=False, indent=2))

if __name__ == "__main__":  
    main()
