import requests
from bs4 import BeautifulSoup
import mod_editais

# URL da página de chamadas públicas abertas do CNPq
url = 'http://memoria2.cnpq.br/web/guest/chamadas-publicas?p_p_id=resultadosportlet_WAR_resultadoscnpqportlet_INSTANCE_0ZaM&p_p_lifecycle=0&p_p_state=normal&p_p_mode=view&p_p_col_id=column-2&p_p_col_pos=1&p_p_col_count=2&filtro=abertas'
print("Coletando", url)

# Envia uma requisição GET para o servidor
response = requests.get(url)
print("RSP", response.status_code)

# Verifica se a requisição foi bem-sucedida
if response.status_code == 200:
    print("Processando Retorno")

    # Cria uma instância de BeautifulSoup
    soup = BeautifulSoup(response.text, 'html.parser')

    # Encontra todas as chamadas públicas
    chamadas = soup.find_all('div', class_='content')

    # Itera sobre as chamadas e extrai informações
    for idx, chamada in enumerate(chamadas, start=1):
        try:
            titulo_chamada = chamada.find('h4').text.strip()
            descricao_chamada = chamada.find('p').text.strip()

            # Extraindo informações de inscrições
            inscricoes = chamada.find('div', class_='inscricao').find('li').text
            print(f'Período de Inscrições: {inscricoes}')

            # Encontra o link permanente
            link_permanente = chamada.find('input', {'type': 'text'})['value'].strip()

            print(f"\nChamada {idx}:")
            print(f"Título: {titulo_chamada}")
            print(f"Descrição: {descricao_chamada}")
            print(f"Inscrições: {inscricoes}")
            print(f"Link Permanente: {link_permanente}")
            #mod_editais.register(1,titulo_chamada,descricao_chamada,inscricoes)
        except AttributeError:
            print(f"\nChamada {idx}: Informações incompletas ou estrutura inesperada.")
else:
    print(f"Erro ao acessar a página. Código de status: {response.status_code}")
