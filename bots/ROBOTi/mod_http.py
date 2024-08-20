import requests

def getURL(url):
    # Faz a requisição HTTP para obter o conteúdo da página
    response = requests.get(url)

    # Verifica se a requisição foi bem-sucedida
    if response.status_code != 200:
        print(f"Erro ao acessar a página: {response.status_code}")
        return

    return response.content