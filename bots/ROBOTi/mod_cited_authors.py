import re

def extract_authors(reference):
    # Regex para capturar os autores antes do título (até o primeiro ponto)
    pattern = r"^([A-Z\.,;\s]+)\."
    match = re.match(pattern, reference)
    if match:
        authors_str = match.group(1)
        # Separar autores por ";" e remover espaços extras
        authors = [author.strip() for author in authors_str.split(";")]
        return authors
    return []

def extract_authors_full(reference):
    # Regex para capturar os autores antes do título
    # Inclui palavras com letras maiúsculas e minúsculas, além de espaços para nomes compostos
    # Padrão para capturar autores no formato "SOBRENOME, Nome"
    pattern = r'([A-ZÁÉÍÓÚÑ]+(?: [A-ZÁÉÍÓÚÑ]+)*),\s*([A-Za-zÁÉÍÓÚáéíóúñÑ]+(?:\s+[A-Za-zÁÉÍÓÚáéíóúñÑ]+)*)'
    match = re.match(pattern, reference)

    if match:
        authors_str = match.group(1)
        # Separar autores por ";" e remover espaços extras
        authors = [author.strip() for author in authors_str.split(";")]
        return authors
    return []
