import re

def locate(text):

    # Padrão regex para identificar URLs
    url_pattern = re.compile(r'http[s]?://(?:[a-zA-Z]|[0-9]|[$-_@.&+]|[!*\\(\\),]|(?:%[0-9a-fA-F][0-9a-fA-F]))+')

    # Encontrar todas as URLs no texto
    urls = re.findall(url_pattern, text)

    return urls