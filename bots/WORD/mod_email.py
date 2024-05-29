import re

def locate(text):

    # Padrão regex para identificar URLs
    email_pattern = re.compile(r'[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}')

    # Encontrar todos os e-mails no texto
    emails = re.findall(email_pattern, text)

    return emails