import requests
from bs4 import BeautifulSoup

def extract_capes_editais(url):
    """
    Extracts the names and URLs of open editais from a specific section of the given CAPES page URL.

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

        # Locate the specific div by its class and ID
        target_div = soup.find('div', class_='tile tile-default', id='c132b11c-75ea-43d4-9615-7f732d7a933e')
        if not target_div:
            print("Target div not found.")
            return []

        # Locate the <ul> within the target div
        ul = target_div.find('ul')
        if not ul:
            print("No <ul> found inside the target div.")
            return []

        # Extract the list items within the <ul>
        editais = []
        for li in ul.find_all('li'):
            a_tag = li.find('a')
            if a_tag:
                edital_name = a_tag.get_text(strip=True)
                edital_url = a_tag['href']

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
