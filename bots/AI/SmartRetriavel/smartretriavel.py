import mod_thesa_v2
import json

pergunta = "o que é RAG de IA em sistemas de bibliotecas "
resultado = mod_thesa_v2.rag_query(pergunta, "data/por.json")
print(json.dumps(resultado, ensure_ascii=False, indent=2))