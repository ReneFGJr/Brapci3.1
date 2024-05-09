# pip install requests beautifulsoup4
import requests
from bs4 import BeautifulSoup

# URL da página de chamadas públicas abertas do CNPq
url = "http://memoria2.cnpq.br/web/guest/chamadas-publicas?p_p_id=resultadosportlet_WAR_resultadoscnpqportlet_INSTANCE_0ZaM&filtro=abertas"
url = 'http://memoria2.cnpq.br/web/guest/chamadas-publicas?p_p_id=resultadosportlet_WAR_resultadoscnpqportlet_INSTANCE_0ZaM&p_p_lifecycle=0&p_p_state=normal&p_p_mode=view&p_p_col_id=column-2&p_p_col_pos=1&p_p_col_count=2&filtro=abertas'
url = 'http://memoria2.cnpq.br/web/guest/chamadas-publicas?p_p_id=resultadosportlet_WAR_resultadoscnpqportlet_INSTANCE_0ZaM&filtro=abertas&detalha=chamadaDivulgada&idDivulgacao=12005'
url = 'http://memoria2.cnpq.br/web/guest/chamadas-publicas?p_p_id=resultadosportlet_WAR_resultadoscnpqportlet_INSTANCE_0ZaM&p_p_lifecycle=0&p_p_state=normal&p_p_mode=view&p_p_col_id=column-2&p_p_col_pos=1&p_p_col_count=2&filtro=abertas'
print("Coletando",url)
# Envia uma requisição GET para o servidor
response = requests.get(url)

print("RSP",response.status_code)
# Verifica se a requisição foi bem-sucedida
if response.status_code == 200:
    # Cria uma instância de BeautifulSoup
    print("Processando Retorno")

    with open('.tmp/__Conteudo.txt', 'w', encoding='utf-8') as arquivo:
        arquivo.write(response.text)
    #print(response.text)
    soup = BeautifulSoup(response.text, 'html.parser')

    # Extraindo informações de inscrições
    inscricoes = soup.find('div', class_='inscricao').find('li').text
    print(f'Período de Inscrições: {inscricoes}')

    # Extraindo título e descrição da chamada
    titulo_chamada = soup.find('div', class_='content').find('h4').text
    descricao_chamada = soup.find('div', class_='content').find('p').text
    print(f'Título: {titulo_chamada}')
    print(f'Descrição: {descricao_chamada}')

    li_element = soup.find('li', {'tabindex': '0'})
    print("Elemento <li> encontrado:", li_element.text.strip())

    h4_element = li_element.find('h4')
    print("Elemento <h4> encontrado:", h4_element.text.strip())

    links = soup.find_all('a')
    for link in links:
        try:
            #print(f'Texto do Link: {link.text}, URL: {link["href"]}')
            print(".",end='')
        except:
            print("ops")
