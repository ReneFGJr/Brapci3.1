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
    pattern = r"^([A-ZÁÉÍÓÚÑáéíóúñ\.,;\s]+)\."
    match = re.match(pattern, reference)
    if match:
        authors_str = match.group(1).strip()

        # Separar autores por ponto e vírgula, ou espaço seguido de letra maiúscula (para nomes sem ponto e vírgula)
        authors = re.split(r';\s*|(?<=\w)\s+(?=[A-Z])', authors_str)

        # Limpar e tratar "et al."
        authors = [author if 'et al' not in author.lower() else 'et al.' for author in authors]

        return [author.strip() for author in authors]

    return []
