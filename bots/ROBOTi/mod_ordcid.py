import requests
import unicodedata

def normalize_string(s):
    """Remove acentos e converte para letras minúsculas."""
    if not s:
        return ""
    return ''.join(
        c for c in unicodedata.normalize('NFD', s.lower())
        if unicodedata.category(c) != 'Mn'
    )

def get_orcid_profile(orcid_id):
    # URL do endpoint público
    url = f"https://pub.orcid.org/v3.0/{orcid_id}"

    # Cabeçalho indicando que queremos os dados em JSON
    headers = {
        "Accept": "application/json"
    }

    try:
        # Fazendo a requisição
        response = requests.get(url, headers=headers)
        response.raise_for_status()  # Verifica se houve erro

        # Retorna os dados do currículo em JSON
        return response.json()

    except requests.exceptions.RequestException as e:
        print(f"Erro ao acessar o ORCID: {e}")
        return None

def search_orcid_by_name(name):
    # Normaliza o nome de entrada
    normalized_name = normalize_string(name)

    # O endpoint é usado com o método GET
    url = f"https://pub.sandbox.orcid.org/v3.0/expanded-search?q='{name}'"
    url = f"https://pub.orcid.org/v3.0/expanded-search?q='{name}'"

    headers = {
        "Accept": "application/json"
    }

    try:
        # Muda para GET
        response = requests.get(url, headers=headers)
        response.raise_for_status()  # Verifica se houve erro na requisição

        # Processa os resultados da busca
        results = response.json().get("expanded-result", [])
        if results:
            print("Perfis encontrados:")
            for result in results:
                # Verifica se os nomes não são None
                given_names = result.get('given-names') or ""
                family_names = result.get('family-names') or ""
                nameOrcID = given_names + " " + family_names

                # Normaliza o nome retornado
                normalized_orcid_name = normalize_string(nameOrcID)

                if normalized_orcid_name == normalized_name:
                    print(f"ORCID ID: {result.get('orcid-id')} - Nome: {nameOrcID}")
                    print("-" * 40)
                    return result.get('orcid-id')
        else:
            print("Nenhum perfil encontrado.")

    except requests.exceptions.RequestException as e:
        print(f"Erro na requisição: {e}")

# Função para salvar o resultado em um arquivo
def save_to_file(filename, content):
    try:
        with open(filename, "w", encoding="utf-8") as file:
            file.write(content)
        print(f"Arquivo salvo com sucesso em {filename}")
    except Exception as e:
        print(f"Erro ao salvar o arquivo: {e}")

# Exemplo de uso
orcid_id = search_orcid_by_name("rene faustino gabriel junior")
profile = get_orcid_profile(orcid_id)
if profile:
    # Converte o JSON para string formatada
    import json
    profile_str = json.dumps(profile, indent=4, ensure_ascii=False)

    # Salva o resultado no arquivo R.txt
    save_to_file("R.txt", profile_str)
