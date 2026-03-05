import re
import os, sys
import json
from pathlib import Path
import pandas as pd
import requests
import unicodedata
from difflib import get_close_matches

OLLAMA_URL = "http://localhost:11434/api/generate"
MODEL = "llama3.2"
#MODEL = "qwen3.5:9b"
#MODEL = "qwen3.5:2b"

VERSION_THESA = "v2.2026.03.04"

BASE_DIR = Path(__file__).resolve().parent

# ========= Recursos de debug =========
def load_thesaurus(json_path):
    """
    Converte o JSON do tesauro em:
    {conceito_canonico: [lista_variacoes]}
    """
    with open(json_path, "r", encoding="utf-8") as f:
        data = json.load(f)

    thesaurus = {}

    for entry in data:
        terms = list(entry.keys())
        canonical = normalize(terms[0])
        variations = [normalize(t) for t in terms]
        thesaurus[canonical] = variations

    return thesaurus


def calculate_concept_relevance(df, thesaurus,
                                weight_title=3.0,
                                weight_keywords=2.0,
                                weight_abstract=1.0):
    """
    Calcula relevância conceitual usando tesauro controlado.
    Retorna ranking + score por conceito.
    """

    results = []

    for _, row in df.iterrows():

        title = normalize(row.get("title", ""))
        abstract = normalize(row.get("abstract", ""))
        keywords = normalize(row.get("keywords", ""))

        doc_score = 0
        concept_hits = {}

        for concept, variations in thesaurus.items():

            pattern = re.compile(
                r"\b(" + "|".join(map(re.escape, variations)) + r")\b"
            )

            count = 0
            count += len(pattern.findall(title)) * weight_title
            count += len(pattern.findall(keywords)) * weight_keywords
            count += len(pattern.findall(abstract)) * weight_abstract

            if count > 0:
                concept_hits[concept] = count
                doc_score += count

        results.append({
            "id": row["id"],
            "year": row.get("year"),
            "title": row.get("title"),
            "relevance_score": doc_score,
            "concepts_found": concept_hits
        })

    result_df = pd.DataFrame(results)

    if result_df["relevance_score"].max() > 0:
        result_df["relevance_norm"] = (
            result_df["relevance_score"] /
            result_df["relevance_score"].max()
        )
    else:
        result_df["relevance_norm"] = 0

    return result_df.sort_values(
        by="relevance_score",
        ascending=False
    )

# ========= Normalização =========
def normalize(text: str) -> str:
    text = text.lower()
    text = unicodedata.normalize("NFD", text)
    text = "".join(c for c in text if unicodedata.category(c) != "Mn")
    return text

# ========= Carregar vocabulário =========
def load_authorized_terms(json_path: str):
    json_file = BASE_DIR / json_path

    # 🔎 Verifica se existe
    if not json_file.exists():
        print(f"❌ Arquivo não encontrado: {json_file}")
        return []

    # 🔎 Verifica se é arquivo (e não diretório)
    if not json_file.is_file():
        print(f"⚠ Caminho não é um arquivo válido: {json_file}")
        return []

    try:
        with open(json_file, "r", encoding="utf-8") as f:
            data = json.load(f)

        terms = []
        for entry in data:
            variant_terms = []
            for term, lang in entry.items():
                # if lang == "por":
                variant_terms.append(term)
            terms.append(variant_terms)

        return terms

    except Exception as e:
        print(f"❌ Erro ao carregar JSON: {e}")
        return []



# ========= Chamada ao Ollama =========
def ollama_interpret(question: str, terms: str):

    tt = ""
    for t in terms:
        linha = " | ".join(t)
        tt += linha + "\n"
    terms = tt.strip()

    prompt = f"""
SYSTEM:
Você é um bibliotecário especializado.

Regras:
- Classifique a pergunta usando APENAS os [TERMOS AUTORIZADOS] mais relevantes.
- Identifique APENAS conceitos centrais.
- NÃO explique suas escolhas.
- NÃO repita os termos extraídos.
- NÃO use frases completas.
- NÃO invente termos.
- TERMOS em plural transforme para singular.
- Selecione apenas termos específicos.
- Retorne APENAS termos conceituais curtos, separados por vírgula.
- NÃO use o termo "indexação" ou "indexação" (ou variações) como resposta, mesmo que seja relevante.

TERMOS AUTORIZADOS:
{terms}

USER:
Extraia os termos relevantes da pergunta: "{question}"
"""

    payload = {
        "model": MODEL,
        "prompt": prompt,
        "keep_alive": "24h",
        "stream": False,
        "options": {
            #            "temperature": 0,
            #            "top_p": 0.1,
            #            "seed": 42
            "temperature": 0.0,
            "top_p": 0.1,
            "seed": 42,
            "think": False
        }
    }

    response = requests.post(
        OLLAMA_URL,
        json=payload,
        timeout=1200
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

# =========
def process_smartretriavel_py(data, thesaurus):
    """
    Replica a lógica do PHP:
    - Junta termos LLM + termos autorizados
    - Normaliza
    - Descobre conceitos
    - Expande todas as variações
    - Retorna ARRAY estruturado (sem operador AND)
    """

    T = {}

    # 🔹 Junta termos
    terms = []
    if "conceitos_interpretados_pelo_llm" in data:
        terms += data["conceitos_interpretados_pelo_llm"]

    if "termos_autorizados_alinhados" in data:
        terms += data["termos_autorizados_alinhados"]

    # 🔹 Normaliza e remove duplicados
    terms = list(set([normalize(t) for t in terms]))

    # 🔹 Descobre conceitos ativados
    for term in terms:
        for concept, variations in thesaurus.items():
            if term in variations:
                if concept not in T:
                    T[concept] = set()

    # 🔹 Expande todas as variações de cada conceito
    for concept in T.keys():
        for variation in thesaurus[concept]:
            T[concept].add(variation)

    # 🔹 Converte para array estruturado
    expanded_array = []

    for concept, variations in T.items():
        expanded_array.append({
            "concept": concept,
            "variations": sorted(list(variations))
        })

    return {
        "conceitos_identificados": list(T.keys()),
        "consulta_expandida_array": expanded_array
    }


# ========= Função principal RAG =========
def rag_query_v2(question: str, json_path: str):

    thesaurus = load_thesaurus(json_path)
    authorized_terms = load_authorized_terms(json_path)

    llm_concepts = ollama_interpret(question, authorized_terms)

    # flatten vocabulary
    flat_terms = [term for group in authorized_terms for term in group]

    aligned_terms = align_with_vocabulary(llm_concepts, flat_terms)

    base_result = {
        "pergunta_original": question,
        "conceitos_interpretados_pelo_llm": llm_concepts,
        "termos_autorizados_alinhados": aligned_terms
    }

    expanded = process_smartretriavel_py(base_result, thesaurus)

    base_result.update(expanded)

    return base_result

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

    except requests.exceptions.RequestException as e:
        print(f"Erro ao acessar a URL: {e}")

    except json.JSONDecodeError:
        print("Erro: a resposta não é um JSON válido")

# ========= Thesa =========
def getThesa(id):
    import os
    from pathlib import Path

    dir = os.getcwd()
    dir = dir.replace(r"public",r"bots/AI/SmartRetriavel")

    if dir.endswith(("/", "\\")):
        dir = dir.rstrip("/\\")  # remove ambos
    caminho = Path(dir)

    if not caminho.exists() and caminho.is_dir():
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
    source = 'thesa_25.json'
    resultado = rag_query(pergunta, source)
    print(json.dumps(resultado, ensure_ascii=False, indent=2))
