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


def split_terms_preserving_quotes(question: str):
    """
    Separa termos por palavra, preservando blocos entre aspas simples ou duplas.
    Exemplo: catalogacao "inteligencia artificial" livros
    -> ["catalogacao", "inteligencia artificial", "livros"]
    """
    if not question:
        return []

    matches = re.findall(r'"([^"]+)"|\'([^\']+)\'|(\S+)', question)
    terms = []
    seen = set()

    for quoted_double, quoted_single, plain in matches:
        token = quoted_double or quoted_single or plain
        token = token.strip()

        # Para tokens fora de aspas, remove pontuação nas bordas.
        if plain:
            token = re.sub(r'^\W+|\W+$', '', token, flags=re.UNICODE)

        if token and token not in seen:
            terms.append(token)
            seen.add(token)

    return terms

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


def align_with_vocabulary_grouped(concepts, authorized_groups, cutoff=0.70):
    """
    Alinha conceitos do LLM aos termos autorizados preservando grupos.
    Retorna lista de grupos (apenas grupos com match), por exemplo:
    [["Catalogação", "Representação Descritiva"], ["IA Generativa"]]
    """
    normalized_vocab = {}
    term_to_groups = {}

    for group_idx, group in enumerate(authorized_groups):
        for term in group:
            norm = normalize(term)
            normalized_vocab[norm] = term
            if norm not in term_to_groups:
                term_to_groups[norm] = []
            if group_idx not in term_to_groups[norm]:
                term_to_groups[norm].append(group_idx)

    grouped_matches = [[] for _ in authorized_groups]

    for concept in concepts:
        norm_concept = normalize(concept)
        matches = get_close_matches(
            norm_concept,
            normalized_vocab.keys(),
            n=1,
            cutoff=cutoff
        )
        for m in matches:
            original_term = normalized_vocab[m]
            for group_idx in term_to_groups.get(m, []):
                if original_term not in grouped_matches[group_idx]:
                    grouped_matches[group_idx].append(original_term)

    return [group for group in grouped_matches if group]


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


def load_net_graph(net_path: str):
    """
    Carrega grafo do arquivo .net (Pajek).

    Retorna:
    - nodes: {node_id: label}
    - children: {node_id: [child_id, ...]}
    """
    full_path = Path(net_path)
    if not full_path.is_absolute():
        full_path = BASE_DIR / net_path

    nodes = {}
    children = {}

    if not full_path.exists() or not full_path.is_file():
        return nodes, children

    in_vertices = False
    in_arcs = False

    with open(full_path, "r", encoding="utf-8") as f:
        for line in f:
            line = line.strip()
            if not line:
                continue

            if line.lower().startswith("*vertices"):
                in_vertices = True
                in_arcs = False
                continue

            if line.lower().startswith("*arcs") or line.lower().startswith("*edges"):
                in_vertices = False
                in_arcs = True
                continue

            if in_vertices:
                m = re.match(r'^(\d+)\s+"([^"]+)"$', line)
                if m:
                    node_id = int(m.group(1))
                    label = m.group(2)
                    nodes[node_id] = label
                    if node_id not in children:
                        children[node_id] = []
                continue

            if in_arcs:
                m = re.match(r'^(\d+)\s+(\d+)', line)
                if m:
                    parent = int(m.group(1))
                    child = int(m.group(2))
                    if parent not in children:
                        children[parent] = []
                    children[parent].append(child)
                    if child not in children:
                        children[child] = []

    return nodes, children


def extract_concept_id_from_label(label: str):
    """
    Extrai o ID conceitual de labels no formato "Termo 123".
    """
    m = re.search(r'\b[Tt]ermo\s+(\d+)\b', label or "")
    if m:
        return m.group(1)
    return None


def recover_specific_terms_by_llm_ids(llm_ids_unicos, net_terms, variantes):
    """
    A partir dos IDs únicos identificados pelo LLM, recupera termos específicos
    no grafo .net (descendentes do nó correspondente a "Termo ID").

    Retorna estrutura por ID:
    {
      "1420": {
         "node_matches": [4],
         "specific_concept_ids": ["1421", "1493"],
         "specific_terms": ["Inteligência Aumentada", "Metadados"]
      }
    }
    """
    nodes, children = load_net_graph(net_terms)

    # Mapa: conceito -> nós do .net cujo label contém "Termo <conceito>"
    concept_to_node_ids = {}
    for node_id, label in nodes.items():
        concept_id = extract_concept_id_from_label(label)
        if concept_id is None:
            continue
        if concept_id not in concept_to_node_ids:
            concept_to_node_ids[concept_id] = []
        concept_to_node_ids[concept_id].append(node_id)

    def dfs_descendants(start_node):
        visited = set()
        stack = list(children.get(start_node, []))
        while stack:
            cur = stack.pop()
            if cur in visited:
                continue
            visited.add(cur)
            stack.extend(children.get(cur, []))
        return visited

    result = {}
    for concept_id in llm_ids_unicos:
        node_matches = concept_to_node_ids.get(str(concept_id), [])
        specific_concepts = set()

        for node_id in node_matches:
            descendants = dfs_descendants(node_id)
            for desc_node in descendants:
                desc_label = nodes.get(desc_node, "")
                desc_concept_id = extract_concept_id_from_label(desc_label)
                if desc_concept_id is not None and desc_concept_id != str(concept_id):
                    specific_concepts.add(desc_concept_id)

        # Traduz IDs para termos (retorna todas as variantes de cada ID)
        sorted_specific_ids = sorted(
            specific_concepts,
            key=lambda x: int(x) if str(x).isdigit() else str(x)
        )

        specific_terms_by_concept = {}
        specific_terms = []

        for sid in sorted_specific_ids:
            terms_for_id = variantes.get(str(sid), [])

            if terms_for_id:
                all_terms = []
                for item in terms_for_id:
                    term = item.get("term", "")
                    if term and term not in all_terms:
                        all_terms.append(term)

                specific_terms_by_concept[str(sid)] = all_terms
                specific_terms.extend(all_terms)
            else:
                specific_terms_by_concept[str(sid)] = [str(sid)]
                specific_terms.append(str(sid))

        result[str(concept_id)] = {
            "node_matches": sorted(node_matches),
            "specific_concept_ids": sorted_specific_ids,
            "specific_terms_by_concept": specific_terms_by_concept,
            "specific_terms": specific_terms,
            "principal_variants": [
                item.get("term", "")
                for item in variantes.get(str(concept_id), [])
                if item.get("term", "")
            ]
        }

    return result


def recover_specific_terms_by_llm_concepts_map(llm_conceptsID, net_terms, variantes):
    """
        Recupera termos específicos preservando o contexto por termo do LLM.

        Entrada esperada:
        {
            "termo llm A": ["1420", "1493"],
            "termo llm B": ["1472"]
        }

        Saída:
        {
            "termo llm A": {
                "ids": ["1420", "1493"],
                "specific_by_id": {
                    "1420": {...},
                    "1493": {...}
                }
            },
            "termo llm B": {
                "ids": ["1472"],
                "specific_by_id": {
                    "1472": {...}
                }
            }
        }
        """
    result = {}
    for llm_term, ids in llm_conceptsID.items():
        ids = [str(i) for i in ids]
        specific_by_id = recover_specific_terms_by_llm_ids(ids, net_terms, variantes)

        # Variantes apenas do ID principal (primeiro ID associado ao termo LLM)
        principal_id = ids[0] if ids else ""
        principal_variants = []
        if principal_id:
            terms_for_id = variantes.get(principal_id, [])
            for item in terms_for_id:
                term = item.get("term", "")
                if term and term not in principal_variants:
                    principal_variants.append(term)

        result[llm_term] = {
                "ids": ids,
                "principal_id": principal_id,
                "principal_variants": principal_variants,
                "specific_by_id": specific_by_id
        }

    return result


def build_estrategia_expansao(llm_specific_terms):
    """
    Gera uma estratégia de expansão em lista única,
    unindo variantes principais + termos específicos por conceito.
    """
    estrategiaF = []

    for _, payload in llm_specific_terms.items():
        estrategia = []
        for term in payload.get("principal_variants", []):
            if term and term not in estrategia:
                estrategia.append(term)

        specific_by_id = payload.get("specific_by_id", {})
        for specific_data in specific_by_id.values():
            terms_map = specific_data.get("specific_terms_by_concept", {})
            for terms in terms_map.values():
                for term in terms:
                    if term and term not in estrategia:
                        estrategia.append(term)
        estrategiaF.append({"variations": estrategia})

    return estrategiaF

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

    # Termos alinhados no vocabulário autorizado
    flat_terms = [term for group in authorized_terms for term in group]
    aligned_terms = align_with_vocabulary(llm_concepts, flat_terms)
    useIA = 1

    # Fallback: se não houver alinhamento com vocabulário,
    # usa termos da pergunta, separando palavra a palavra,
    # mas preservando expressões entre aspas.
    if not aligned_terms:
        aligned_terms = align_with_vocabulary(split_terms_preserving_quotes(question), flat_terms)
        useIA = 0
        for t in aligned_terms:
            print(t)
        sys.exit(0)

    llm_specific_terms_by_id = recover_specific_terms_by_llm_ids(llm_ids_unicos, net_terms, variantes)
    llm_specific_terms = recover_specific_terms_by_llm_concepts_map(llm_conceptsID, net_terms, variantes)

    estrategia_expansao = build_estrategia_expansao(llm_specific_terms)



    base_result = {
        "pergunta_original": question,
        "conceitos_interpretados_pelo_llm": llm_concepts,
    #        "llm_ids_unicos": llm_ids_unicos,
    #        "llm_specific_terms_by_id": llm_specific_terms_by_id,
    #        "llm_specific_terms": llm_specific_terms,
        "estrategia_expansao": estrategia_expansao,
        "termos_autorizados_alinhados": aligned_terms,
    #        "variantes_carregadas": variantes,
        "total_ids_conceito": len(variantes),
        "total_variantes": sum(len(v) for v in variantes.values()),
        "use_ia": useIA
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
    resultado = rag_query_v2(pergunta, source)
    print(json.dumps(resultado, ensure_ascii=False, indent=2))
