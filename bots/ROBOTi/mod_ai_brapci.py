import requests

def chat(message):
    # URL da API que você quer consultar
    url = "https://cip.brapci.inf.br/api/ai/chat"

    params = {
        "message": message
    }

    # Envie a requisição GET
    response = requests.get(url, params=params)

    # Verifique o status da resposta
    if response.status_code == 200:
        # Se a requisição for bem-sucedida, processar o JSON retornado
        data = response.json()
        print(data)
    else:
        # Caso haja um erro, exibir o status code
        print(f"Erro: {response.status_code}")