import requests
from bs4 import BeautifulSoup
import csv

# URL da página
url = "http://memoria2.cnpq.br/web/guest/chamadas-publicas"

# Fazendo a requisição à página
response = requests.get(url)
soup = BeautifulSoup(response.content, 'html.parser')

# Lista para armazenar os dados
editais = []

# Encontrando os títulos, links e datas de inscrição dos editais
for edital in soup.find_all('h4'):  # Busca os títulos
    titulo = edital.text.strip()  # Extrai o texto do título

    # Busca o campo input com o link
    link_input = edital.find_next('input', {'type': 'text'})
    link = link_input['value'] if link_input and 'value' in link_input.attrs else None

    # Busca as datas de inscrição
    inscricao_div = edital.find_next('div', class_='inscricao')
    datas = None
    if inscricao_div:
        datas_list = inscricao_div.find('ul', class_='datas')
        if datas_list:
            datas = datas_list.text.strip()  # Extrai o texto das datas

    # Adiciona os dados coletados à lista
    editais.append([titulo, link, datas])

# Criando o arquivo CSV
with open('../../.tmp/editais_cnpq.csv', mode='w', newline='', encoding='utf-8') as file:
    writer = csv.writer(file)
    writer.writerow(['title', 'link', 'dates'])
    writer.writerows(editais)

print("Arquivo CSV gerado com sucesso!")
