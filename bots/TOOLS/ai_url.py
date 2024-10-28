import re

def extrair_urls(txt):
    # Expressão regular para detectar URLs
    padrao_url = r'(https?://[^\s]+)'

    urls = []
    urls.extend(re.findall(padrao_url, txt))

    return urls