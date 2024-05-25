import re

def words():
    texto = "Este é um exemplo de texto, para separar em palavras!"
    # Remove caracteres especiais
    texto_limpo = re.sub(r'[^\w\s]', '', texto)
    palavras = texto_limpo.split()
    print(palavras)