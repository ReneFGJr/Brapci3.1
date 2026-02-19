import json
import requests
import unicodedata
import re
from pathlib import Path
from difflib import get_close_matches

OLLAMA_URL = "http://localhost:11434/api/generate"
MODEL = "llama3.2"

BASE_DIR = Path(__file__).resolve().parent

# ========= Thesa =========
def getThesa(id):
    url = "https://www.ufrgs.br/thesa/api/ai_rag_json/"+str(id)+"/por"
    arquivo_saida = "data/por.json"
    download_json(url, arquivo_saida)

# ========= Normalização =========
def normalize(text: str) -> str:
    text = text.lower()
    text = unicodedata.normalize("NFD", text)
    text = "".join(c for c in text if unicodedata.category(c) != "Mn")
    return text


# ========= Carregar vocabulário =========
def load_authorized_terms(json_path: str):
    json_file = BASE_DIR / json_path

    with open(json_file, "r", encoding="utf-8") as f:
        data = json.load(f)

    terms = []
    for entry in data:
        for term, lang in entry.items():
            #if lang == "por":
            terms.append(term)
    return terms


# ========= Chamada ao Ollama =========
def ollama_interpret(question: str):
    prompt = f"""
Você é um sistema de apoio à indexação controlada.

Regras:
- Identifique APENAS conceitos centrais
- NÃO explique
- NÃO use frases completas
- NÃO invente termos
- Retorne APENAS termos conceituais curtos, separados por vírgula

Pergunta:
{question}
"""

    payload = {
        "model": MODEL,
        "prompt": prompt,
        "stream": False,
        "options": {
            "temperature": 0,
            "top_p": 0.1,
            "seed": 42
        }
    }

    response = requests.post(
        OLLAMA_URL,
        json=payload,
        timeout=60
    )
    response.raise_for_status()

    text = response.json()["response"]
    concepts = [c.strip() for c in text.split(",") if c.strip()]
    return concepts


# ========= Alinhamento com vocabulário =========
def align_with_vocabulary(concepts, authorized_terms, cutoff=0.70):
    normalized_vocab = {normalize(t): t for t in authorized_terms}
    matched_terms = set()

    for concept in concepts:
        norm_concept = normalize(concept)
        matches = get_close_matches(
            norm_concept,
            normalized_vocab.keys(),
            n=3,
            cutoff=cutoff
        )
        for m in matches:
            matched_terms.add(normalized_vocab[m])

    return sorted(matched_terms)


# ========= Função principal RAG =========
def rag_query(question: str, json_path: str):
    authorized_terms = load_authorized_terms(json_path)

    llm_concepts = ollama_interpret(question)
    aligned_terms = align_with_vocabulary(llm_concepts, authorized_terms)

    return {
        "pergunta_original": question,
        "conceitos_interpretados_pelo_llm": llm_concepts,
        "termos_autorizados_alinhados": aligned_terms,
        "modelo_llm": "llama3.2 (Ollama)",
        "fonte_vocabulario": "por.json"
    }

# ========= Download Library ======
def download(url: str, output_file: str) -> None:
    """
    Faz o download de uma URL que retorna TEXTO
    e salva o conteúdo em um arquivo.
    """

    try:
        response = requests.get(url, timeout=30)
        response.raise_for_status()

        # Garante que o diretório existe
        output_path = Path(output_file)
        output_path.parent.mkdir(parents=True, exist_ok=True)

        # Salva como texto
        with output_path.open("w", encoding="utf-8") as f:
            f.write(response.text)

        print(f"Arquivo salvo com sucesso em: {output_path}")

    except requests.exceptions.RequestException as e:
        print(f"Erro ao acessar a URL: {e}")
        print("URL: "+url)

        
def download_json(url: str, output_file: str) -> None:
    """
    Faz o download de uma URL que retorna JSON
    e salva o conteúdo em um arquivo .json

    :param url: URL da API
    :param output_file: caminho do arquivo JSON de saída
    """

    try:
        response = requests.get(url, timeout=30)
        response.raise_for_status()  # erro HTTP (404, 500, etc.)

        # Converte resposta para JSON
        data = response.json()

        # Garante que o diretório existe
        output_path = Path(output_file)
        output_path.parent.mkdir(parents=True, exist_ok=True)

        # Salva em arquivo JSON formatado
        with output_path.open("w", encoding="utf-8") as f:
            json.dump(data, f, ensure_ascii=False, indent=2)

        print(f"Arquivo salvo com sucesso em: {output_path}")

    except requests.exceptions.RequestException as e:
        print(f"Erro ao acessar a URL: {e}")

    except json.JSONDecodeError:
        print("Erro: a resposta não é um JSON válido")

def getThesa(id):
    import os
    from pathlib import Path
    
    dir = os.getcwd()
    dir = dir.replace(r"public",r"bots/AI/SmartRetriavel") 
    print(dir)
    if dir.endswith(("/", "\\")):
        dir = caminho.rstrip("/\\")  # remove ambos    
    caminho = Path(dir)

    if not caminho.exists() and caminho.is_dir():
        print("Directory not found!")
        return ""
    
    
    url = "https://www.ufrgs.br/thesa/api/ai_rag2_json/"+str(id)+"/por"
    arquivo_saida = dir + "/data/thesa_"+str(id)+".json"
    download_json(url, arquivo_saida)

    url = "https://www.ufrgs.br/thesa/api/ai_terms_json/"+str(id)+"/por"
    arquivo_saida = dir + "/data/thesa_"+str(id)+"_terms.json"
    download_json(url, arquivo_saida)

    url = "https://www.ufrgs.br/thesa/api/ai_pajek/"+str(id)+"/net"
    arquivo_saida = dir + "/data/thesa_"+str(id)+".net"
    download(url, arquivo_saida)          


# ========= Execução =========
if __name__ == "__main__":
    pergunta = "Como a IAG é utilizada na catalogação de livros?"
    resultado = rag_query(pergunta, "data/por.json")
    print(json.dumps(resultado, ensure_ascii=False, indent=2))
