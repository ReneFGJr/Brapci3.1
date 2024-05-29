import pyphen

# Carregar o dicionário de hífen para português
dic = pyphen.Pyphen(lang='pt_BR')

# Exemplo de texto
text = """
Aproximações da produção científica em ciências da saúde na ciência da
informação no Brasil.
"""

# Função para separar texto em sílabas
def separa_silabas(texto):
    palavras = texto.split()
    palavras_silabas = [dic.inserted(palavra) for palavra in palavras]
    return ' '.join(palavras_silabas)

# Separar o texto em sílabas
texto_silabas = separa_silabas(text)

print(texto_silabas)