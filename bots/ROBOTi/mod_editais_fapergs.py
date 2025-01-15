import requests
from bs4 import BeautifulSoup
import pandas as pd

def editais_fapergs():
    # URL da página de editais abertos da FAPERGS
    url = 'https://fapergs.rs.gov.br/abertos'
    url = 'https://fapergs.rs.gov.br/_service/conteudo/pagedlistfilho?id=2042&templatename=pagina.listapagina.padrao&currentPage=1&pageSize=10'

    # Extraindo os editais
    editais = extract_fapergs_editais(url)

    # Convertendo para DataFrame para melhor visualização e salvando em CSV
    if editais:
        df = pd.DataFrame(editais)
        df.to_csv('editais_fapergs.csv', index=False, encoding='utf-8')
        print("Editais extraídos e salvos em 'editais_fapergs.csv'")
    else:
        print("Nenhum edital encontrado ou ocorreu um erro.")

def extract_fapergs_editais(url):
    try:
        # Fazendo a solicitação ao site
        response = requests.get(url)
        response.raise_for_status()  # Verifica erros na requisição

        # Parseando o conteúdo JSON
        data = response.content
        data = data.json()
        print(data)

        # Parseando o conteúdo HTML
        soup = BeautifulSoup(response.content, 'html.parser')

        # Localizando a seção dos editais
        editais_section = soup.find_all('div', class_='lista-edital')

        editais = []
        for edital in editais_section:
            # Extraindo título, link e outras informações relevantes
            titulo = edital.find('h3').get_text(strip=True)
            link = edital.find('a')['href']
            data = edital.find('p', class_='data').get_text(strip=True) if edital.find('p', class_='data') else 'Não informado'

            editais.append({
                'Título': titulo,
                'Link': f"https://fapergs.rs.gov.br{link}",
                'Data': data
            })

        return editais

    except requests.exceptions.RequestException as e:
        print(f"Erro ao acessar o site: {e}")
        return []

editais_fapergs()