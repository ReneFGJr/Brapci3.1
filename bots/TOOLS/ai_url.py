import re

def extrair_urls(nome_arquivo):
    # Expressão regular para detectar URLs
    padrao_url = r'(https?://[^\s]+)'

    urls = []

    # Lê o arquivo e busca URLs
    try:
        with open(nome_arquivo, 'r', encoding='utf-8') as arquivo:
            for linha in arquivo:
                # Procura todas as URLs na linha e adiciona à lista
                urls.extend(re.findall(padrao_url, linha))

    except FileNotFoundError:
        print(f"O arquivo '{nome_arquivo}' não foi encontrado.")
    except Exception as e:
        print(f"Ocorreu um erro: {e}")

    return urls