import json
from playwright.sync_api import sync_playwright

# Função principal que usará o Playwright
def extract_marc21_data():
    # URL da página
    url = 'https://biblio.cultura.rs.gov.br/cgi-bin/koha/opac-MARCdetail.pl?biblionumber=29865'

    # Inicializar o Playwright
    with sync_playwright() as p:
        # Abrir o navegador
        browser = p.chromium.launch(headless=True)  # headless=True para não abrir uma janela
        page = browser.new_page()

        # Acessar a página
        page.goto(url)

        # Esperar que a tabela MARC seja carregada (baseado no seletor da tabela)
        page.wait_for_selector('table#marcdetail')

        # Obter o conteúdo HTML renderizado da página
        content = page.content()

        # Fechar o navegador
        browser.close()

        # Continuar o processamento com BeautifulSoup
        from bs4 import BeautifulSoup
        soup = BeautifulSoup(content, 'html.parser')

        # Estrutura onde armazenaremos os dados
        marc_data = []

        # Encontrar a tabela onde os dados MARC estão presentes
        marc_table = soup.find('table', {'id': 'marcdetail'})

        # Verificar se a tabela foi encontrada
        if marc_table:
            rows = marc_table.find_all('tr')

            for row in rows:
                # Extrair as colunas de cada linha da tabela
                cols = row.find_all('td')

                if len(cols) >= 5:
                    field = cols[0].text.strip()
                    subfield = cols[1].text.strip()
                    indicator_1 = cols[2].text.strip()
                    indicator_2 = cols[3].text.strip()
                    content = cols[4].text.strip()

                    # Estruturar as informações
                    marc_entry = {
                        'field': field,
                        'subfield': subfield,
                        'indicator_1': indicator_1,
                        'indicator_2': indicator_2,
                        'content': content
                    }

                    # Adicionar à lista de dados
                    marc_data.append(marc_entry)

        # Converter para JSON
        with open('marc_data.json', 'w', encoding='utf-8') as f:
            json.dump(marc_data, f, ensure_ascii=False, indent=4)

        print('Dados extraídos e salvos em "marc_data.json".')

# Chamar a função para realizar a extração
extract_marc21_data()
