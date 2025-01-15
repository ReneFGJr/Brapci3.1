from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.keys import Keys
from webdriver_manager.chrome import ChromeDriverManager
import time

def extract_capes_editais_selenium(url):
    """
    Extracts the names and URLs of open editais from the given CAPES page URL using Selenium.

    Args:
        url (str): URL of the CAPES editais page.

    Returns:
        list of dict: A list containing dictionaries with 'name' and 'url' keys.
    """
    # Configure Selenium WebDriver
    options = Options()
    options.add_argument('--headless')  # Run in headless mode
    options.add_argument('--disable-gpu')
    options.add_argument('--no-sandbox')
    driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=options)

    try:
        # Load the webpage
        driver.get(url)
        time.sleep(3)  # Wait for the page to load fully

        # Extract edital links
        editais = []
        links = driver.find_elements(By.TAG_NAME, 'a')

        for link in links:
            text = link.text.strip()
            href = link.get_attribute('href')

            if text and 'edital' in text.lower():
                editais.append({'name': text, 'url': href})

        return editais

    except Exception as e:
        print(f"An error occurred: {e}")
        return []

    finally:
        driver.quit()

# Example usage
url = "https://www.gov.br/capes/pt-br/assuntos/editais-e-resultados-capes"
editais_list = extract_capes_editais_selenium(url)

for edital in editais_list:
    print(f"Name: {edital['name']}, URL: {edital['url']}")
