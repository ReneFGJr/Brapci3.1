import requests
from bs4 import BeautifulSoup


def extract_marc_records(html: str) -> list:
    """
    Extrai o conteúdo de todos os elementos <pre class="marc line"> do HTML fornecido.

    Parâmetros:
      html (str): O conteúdo HTML a ser processado.

    Retorna:
      list: Uma lista com o texto contido em cada elemento <pre class="marc line">, ou uma lista vazia caso nenhum elemento seja encontrado.
    """
    soup = BeautifulSoup(html, 'html.parser')
    pre_tags = soup.find_all("pre", class_="marc line")

    if pre_tags:
        return [tag.get_text(strip=True) for tag in pre_tags]
    else:
        return []


def consulta_zbib(isbn: str):
    """
    Consulta o catálogo Zbib da UFSC utilizando o ISBN e retorna os resultados extraídos do HTML.

    Parâmetros:
      isbn (str): O ISBN do livro a ser pesquisado.

    Retorna:
      list: Uma lista de strings com os resultados encontrados, ou uma mensagem de erro se nenhum resultado for encontrado.
    """
    base_url = "https://catalogo.bu.ufsc.br/zbib/"
    params = {
        "searchString": isbn,
        "searchType": "7",  # Busca por ISBN
        "operator": "and",
        "searchString2": "",
        "searchType2": "1003",
        "targets[0]": "on",
        "targets[1]": "on",
        "targets[2]": "on",
        "targets[3]": "on",
        "targets[4]": "on",
        "targets[5]": "on",
        "targets[6]": "on",
        "targets[7]": "on",
        "targets[8]": "on",
        "targets[9]": "on",
        "targets[10]": "on",
        "targets[11]": "on",
        "targets[12]": "on",
        "targets[13]": "on",
        "targets[14]": "on",
        "targets[15]": "on",
        "targets[16]": "on",
        "targets[17]": "on",
        "targets[18]": "on",
        "targets[19]": "on",
        "targets[20]": "on"
    }

    print("Buscando ISBN:", isbn)

    try:
        response = requests.get(base_url, params=params)
        response.raise_for_status()  # Levanta exceção para status HTTP de erro
    except requests.RequestException as error:
        return f"Erro na requisição: {error}"

    # Extrai todos os resultados <pre class="marc line"> encontrados
    resultados = extract_marc_records(response.text)

    if not resultados:
        return "Nenhum resultado encontrado."

    return resultados


# Exemplo de uso:
if __name__ == "__main__":
    # Exemplo com ISBN fornecido diretamente; também pode ser solicitado via input()
    isbn_input = '9788573597691'
    resultado = consulta_zbib(isbn_input)

    if isinstance(resultado, list):
        print("Resultados encontrados:")
        for res in resultado:
            print("[] " + res)
    else:
        print(resultado)
