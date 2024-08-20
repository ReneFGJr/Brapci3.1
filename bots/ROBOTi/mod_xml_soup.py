from bs4 import BeautifulSoup

def readMeta(txt,meta):

    # Cria um objeto BeautifulSoup para analisar o HTML
    soup = BeautifulSoup(txt, 'html.parser')

    # Extrai os metadados específicos
    values = soup.find_all('meta', attrs={'name': meta})

    return values
