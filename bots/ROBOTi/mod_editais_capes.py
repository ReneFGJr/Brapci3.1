import requests
from bs4 import BeautifulSoup

def extract_capes_editais(url):
    """
    Extracts the names and URLs of open editais from the given CAPES page URL.

    Args:
        url (str): URL of the CAPES editais page.

    Returns:
        list of dict: A list containing dictionaries with 'name' and 'url' keys.
    """
    try:
        # Send a GET request to the provided URL
        response = requests.get(url)
        response.raise_for_status()

        # Parse the HTML content
        soup = BeautifulSoup(response.content, 'html.parser')

        print(response.content)

        # Find the relevant sections containing the edital information
        editais = []
        for link in soup.find_all('li', href=True):
            if 'edital' in link.get_text().lower():  # Filtering links with "edital" in the text
                edital_name = link.get_text(strip=True)
                edital_url = link['href']

                # Ensure full URL if the link is relative
                if not edital_url.startswith('http'):
                    edital_url = url.split('/pt-br/')[0] + edital_url

                editais.append({
                    'name': edital_name,
                    'url': edital_url
                })

        return editais

    except requests.RequestException as e:
        print(f"An error occurred while fetching the page: {e}")
        return []

# Example usage
url = "https://www.gov.br/capes/pt-br/assuntos/editais-e-resultados-capes"
editais_list = extract_capes_editais(url)
for edital in editais_list:
    print(f"Name: {edital['name']}, URL: {edital['url']}")
