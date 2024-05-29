import re
import mod_links
import mod_email
import mod_doi
import mod_frase

def words():
    file = 'txt/sample.txt'
    with open(file, 'r', encoding='utf-8') as file:
        texto = file.read()



    links = mod_links.locate(texto)
    email = mod_email.locate(texto)
    dois = mod_doi.locate(texto)

    print("LINK",links)
    print("================")
    print("e-mail",email)
    print("================")
    print("DOI",dois)
    print("================")

    texto = remove(texto,links)
    texto = remove(texto,email)
    texto = remove(texto,dois)

    mod_frase.process(texto)
    quit()

    # Remove caracteres especiais
    texto_limpo = re.sub(r'[^\w\s]', '', texto)
    palavras = texto_limpo.split()

    print(palavras)

# Função para remover DOIs do texto
def remove(text, vars):
    for var in vars:
        text = re.sub(re.escape(var), ' ', text)
    return text