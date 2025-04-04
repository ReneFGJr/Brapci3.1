import requests


def extrair_palavras_chave(texto,
                           modelo='llama3.3',
                           temperatura=0,
                           url='https://ollama.brapci.inf.br/api/chat'):
    # Mensagem no formato do Ollama
    print("   Modelo Ollama:",modelo)

    prompt = f"Extraia cinco palavras-chave desse texto, inclua 'palavras-chave:' ao mostrá-las: "

    mensagens = [{
        "role": "user",
        "content": prompt
    },{
        "role": "user",
        "content": texto
    }]

    print(mensagens)

    # Payload da requisição
    payload = {
        "model": modelo,
        "messages": mensagens,
        "options": {
            "temperature": temperatura
        },
        "stream": False
    }

    try:
        resposta = requests.post(url, json=payload)
        resposta.raise_for_status()
        resultado = resposta.json()

        # O conteúdo da resposta estará em `message.content` (depende da API do Ollama usada)
        conteudo = resultado.get("message",
                                 {}).get("content", "Nenhuma resposta obtida.")
        return conteudo

    except requests.exceptions.RequestException as erro:
        return f"Erro na requisição: {erro}"


# Exemplo de uso
if __name__ == "__main__":
    texto_exemplo = """
    A bibliometria é uma ferramenta essencial para a avaliação da produção científica em diversas áreas do conhecimento.
    Este estudo tem como objetivo analisar a produção científica brasileira na área da Ciência da Informação nos últimos 10 anos.
    """

    resultado = extrair_palavras_chave(texto_exemplo)
    print("\nPalavras-chave extraídas:\n")
    print(resultado)
