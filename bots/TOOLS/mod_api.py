import requests

def api_post(url, dados):
    try:
        # Envia a requisição POST com os dados
        resposta = requests.post(url, json=dados)

        # Verifica se a requisição foi bem-sucedida
        if resposta.status_code == 200:
            return resposta.json()  # Retorna a resposta em JSON se disponível
        else:
            return {"erro": f"Falha ao enviar dados. Código de status: {resposta.status_code}"}
    except Exception as e:
        return {"erro": f"Ocorreu um erro: {str(e)}"}