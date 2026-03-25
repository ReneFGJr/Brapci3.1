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

# ========= Carregar Todas as Variantes (Agrupadas por ID) =========
def load_all_variants(terms_json_path: str) -> dict:
    """
    Lê arquivo de termos JSON e cria um dicionário agrupado por ID de conceito.

    Retorna estrutura:
    {
        "322": [
            {
                "id": "0",
                "term": "ChatGPT",
                "normalized": "chatgpt"
            }
        ],
        "1420": [
            {
                "id": "4",
                "term": "Audiobooks",
                "normalized": "audiobooks"
            },
            {
                "id": "5",
                "term": "Audiolivros",
                "normalized": "audiolivros"
            }
        ]
    }
    """
    json_file = BASE_DIR / terms_json_path
    variantes = {}

    try:
        if not json_file.exists():
            print(f"❌ Arquivo não encontrado: {json_file}")
            return variantes

        with open(json_file, "r", encoding="utf-8") as f:
            data = json.load(f)

        entries = []
        if isinstance(data, dict):
            entries = list(data.items())
        elif isinstance(data, list):
            entries = [(str(idx), entry_data) for idx, entry_data in enumerate(data)]

        for entry_id, entry_data in entries:
            if not isinstance(entry_data, dict):
                continue

            concept_id = str(entry_data.get("concept", "")).strip()
            term = str(entry_data.get("term", "")).strip()
            if concept_id == "" or term == "":
                continue

            if concept_id not in variantes:
                variantes[concept_id] = []

            variantes[concept_id].append({
                "id": str(entry_id),
                "term": term,
                "normalized": normalize(term)
            })

        total_terms = sum(len(v) for v in variantes.values())
        print(f"✓ {total_terms} variantes carregadas de {terms_json_path}")
        return variantes

    except json.JSONDecodeError as e:
        print(f"❌ Erro ao decodificar JSON: {e}")
        return variantes

    except Exception as e:
        print(f"❌ Erro ao carregar variantes: {e}")
        return variantes



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


# ========= Semantica com vocabulário controlado =========
def align_with_vocabulary(concepts, authorized_terms, cutoff=0.70):
    normalized_vocab = {normalize(t): t for t in authorized_terms}
    matched_terms = set()

    for concept in concepts:
        norm_concept = normalize(concept)
        matches = get_close_matches(
            norm_concept,
            normalized_vocab.keys(),
            n=1,
            cutoff=cutoff
        )
        for m in matches:
            matched_terms.add(normalized_vocab[m])

    return sorted(matched_terms)


def map_llm_concepts_to_ids(llm_concepts, variantes, cutoff=0.85):
    """
    Compara conceitos vindos do LLM com variantes e recupera IDs de conceito.

    Retorna:
    - llm_concepts_id: dict {"termo_llm": ["id_conceito", ...]}
    - ids_unicos: lista ordenada de IDs encontrados
    """
    term_to_ids = {}

    for concept_id, terms in variantes.items():
        for item in terms:
            term_norm = item.get("normalized", "")
            if not term_norm:
                continue

            if term_norm not in term_to_ids:
                term_to_ids[term_norm] = set()
            term_to_ids[term_norm].add(str(concept_id))

    llm_concepts_id = {}
    ids_unicos = set()

    vocab_norm = list(term_to_ids.keys())
    for llm_term in llm_concepts:
        llm_norm = normalize(llm_term)
        ids_found = set()

        # Match exato normalizado
        if llm_norm in term_to_ids:
            ids_found.update(term_to_ids[llm_norm])
        else:
            # Match aproximado para reduzir perdas por pequenas variações
            matches = get_close_matches(llm_norm, vocab_norm, n=3, cutoff=cutoff)
            for m in matches:
                ids_found.update(term_to_ids[m])

        ids_list = sorted(ids_found, key=lambda x: int(x) if x.isdigit() else x)
        llm_concepts_id[llm_term] = ids_list
        ids_unicos.update(ids_found)

    ids_unicos = sorted(ids_unicos, key=lambda x: int(x) if x.isdigit() else x)
    return llm_concepts_id, ids_unicos

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

    json_terms = json_path.replace('.json', '_terms.json')
    net_terms = json_path.replace('.json', '.net')

    # 🔹 NOVO: Carrega TODAS as variantes do JSON em um array
    variantes = load_all_variants(json_terms)

    # Carrega o tesauro e os termos autorizados
    thesaurus = load_thesaurus(json_path)
    authorized_terms = load_authorized_terms(json_path)

    llm_concepts = ollama_interpret(question, authorized_terms)
    llm_conceptsID, llm_ids_unicos = map_llm_concepts_to_ids(llm_concepts, variantes)

    print(llm_conceptsID)
    print(llm_ids_unicos)
    sys.exit()



    # flatten vocabulary
    flat_terms = [term for group in authorized_terms for term in group]

    aligned_terms = align_with_vocabulary(llm_concepts, flat_terms)
    #aligned_terms = llm_concepts
    print("Conceitos Alinhados:", aligned_terms)
    if (not os.path.exists(json_path)):
        print("Erro: arquivo do tesauro não encontrado.")
        sys.exit(1)

    if (not os.path.exists(json_terms)):
        print("Erro: arquivo de termos autorizados não encontrado.")
        sys.exit(1)

    if (not os.path.exists(net_terms)):
        print("Erro: arquivo .net não encontrado.")
        sys.exit(1)

    base_result = {
        "pergunta_original": question,
        "conceitos_interpretados_pelo_llm": llm_concepts,
        "llm_conceptsID": llm_conceptsID,
        "llm_ids_unicos": llm_ids_unicos,
        "termos_autorizados_alinhados": aligned_terms,
        "variantes_carregadas": variantes,
        "total_ids_conceito": len(variantes),
        "total_variantes": sum(len(v) for v in variantes.values())
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
