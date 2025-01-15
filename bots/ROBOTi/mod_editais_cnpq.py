import requests
from bs4 import BeautifulSoup
import mod_editais

# URL da página de chamadas públicas abertas do CNPq
url = "http://memoria2.cnpq.br/web/guest/chamadas-publicas?p_p_id=resultadosportlet_WAR_resultadoscnpqportlet_INSTANCE_0ZaM&filtro=abertas"
print("Coletando", url)

# Envia uma requisição GET para o servidor
response = requests.get(url)

print("RSP", response.status_code)

# Verifica se a requisição foi bem-sucedida
if response.status_code == 200:
    print("Processando Retorno")

    # Cria uma instância de BeautifulSoup

    # Carregar o arquivo HTML
    content = response.text
    print(content)

    # Analisar o conteúdo HTML
    soup = BeautifulSoup(content, 'html.parser')

    # Estruturas para armazenar os dados extraídos
    calls = []

    # Iterar sobre os elementos relevantes no HTML (ajuste os seletores conforme necessário)
    for call in soup.find_all('div', class_='call-container'):  # Substitua pela classe correta
        name = call.find('h3').get_text(strip=True) if call.find('h3') else 'N/A'
        date = call.find('span', class_='date').get_text(strip=True) if call.find('span', class_='date') else 'N/A'
        description = call.find('p', class_='description').get_text(strip=True) if call.find('p', class_='description') else 'N/A'
        link = call.find('a', href=True)['href'] if call.find('a', href=True) else 'N/A'

        calls.append({
            'Nome da chamada': name,
            'Data das inscrições': date,
            'Descrição do edital': description,
            'Link permanente': link
        })

    # Exibir os resultados
    for call in calls:
        print(f"Nome da chamada: {call['Nome da chamada']}")
        print(f"Data das inscrições: {call['Data das inscrições']}")
        print(f"Descrição do edital: {call['Descrição do edital']}")
        print(f"Link permanente: {call['Link permanente']}")
        print("-" * 40)