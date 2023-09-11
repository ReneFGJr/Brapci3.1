import requests
from bs4 import BeautifulSoup

# initialize the list of discovered urls
# with the first page to visit
urls = ["https://bpe.biblioteca.site/opac/php/buscar_integrada.php?lang=pt&base=acervo&modo=1B&alcance=and&Opcion=libre&prefijo=TW_&Sub_Expresion=0024508"]

# until all pages have been visited
while len(urls) != 0:
    # get the page to visit from the list
    current_url = urls.pop()

    # crawling logic
    response = requests.get(current_url, verify=False)
    soup = BeautifulSoup(response.content, "html.parser")

    link_elements = soup.select("div")

    for link_element in link_elements:
        #url = link_element['div']
        print('==',link_element,chr(13))