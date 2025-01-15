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
    soup = BeautifulSoup(response.text, 'html.parser')

    # Busca todos os blocos que contêm editais
    editais = soup.find_all('div', class_='content')

    if not editais:
        print("Nenhum edital encontrado na página.")

    for idx, edital in enumerate(editais):
        try:
            # Extraindo título da chamada
            titulo_chamada = edital.find('h4').text.strip()

            # Extraindo descrição da chamada
            descricao_chamada = edital.find('p').text.strip()

            # Extraindo informações adicionais, como período de inscrições
            inscricoes_element = edital.find('div', class_='inscricao')
            inscricoes = inscricoes_element.text.strip() if inscricoes_element else "Informação não disponível"

            # Extraindo link permanente do edital
            link_permanente_element = edital.find('div', class_='link-permanente')
            if link_permanente_element:
                link_permanente_input = link_permanente_element.find('input', {'type': 'text'})
                link_permanente = link_permanente_input['value'] if link_permanente_input else "Link não disponível"
            else:
                link_permanente = "Link não disponível"

            print(f"\nEdital {idx + 1}:")
            print(f"Título: {titulo_chamada}")
            print(f"Descrição: {descricao_chamada}")
            print(f"Período de Inscrições: {inscricoes}")
            print(f"Link Permanente: {link_permanente}")

            # Registrar no módulo mod_editais
            mod_editais.register(idx + 1, titulo_chamada, descricao_chamada, 'Aberto', link_permanente)

        except Exception as e:
            print(f"Erro ao processar edital {idx + 1}: {e}")

    # Coletar todos os links na página para referência
    #links = soup.find_all('a')
    #for link in links:
    #    try:
    #        print(f"Texto do Link: {link.text.strip()}, URL: {link['href']}")
    #    except Exception as e:
    #        print(f"Erro ao processar link: {e}")
else:
    print("Falha na requisição. Verifique a URL ou a conexão com a internet.")
