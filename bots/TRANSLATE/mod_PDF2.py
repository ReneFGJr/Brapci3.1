import fitz  # Importa PyMuPDF
import re

# Função para extrair texto do PDF
def extrair_texto_pdf(caminho_arquivo):
    texto = ""
    with fitz.open(caminho_arquivo) as doc:
        for pagina in doc:
            texto += pagina.get_text()
    return texto

# Função para encontrar metadados no texto
def encontrar_metadados(texto):
    metadados = {}

    # Exemplo de extração usando expressões regulares
    titulo_match = re.search(r"título do documento:\s*(.*)", texto, re.IGNORECASE)
    autores_match = re.search(r"autores:\s*(.*)", texto, re.IGNORECASE)

    if titulo_match:
        metadados['Título'] = titulo_match.group(1)
    if autores_match:
        metadados['Autores'] = autores_match.group(1).split(',')

    return metadados

# Caminho para o arquivo PDF
caminho_arquivo_pdf = '257789.pdf'

# Extrair texto do PDF
texto_pdf = extrair_texto_pdf(caminho_arquivo_pdf)

# Encontrar metadados no texto extraído
metadados_encontrados = encontrar_metadados(texto_pdf)

# Imprimir os metadados encontrados
print(metadados_encontrados)

def extrair_titulo_por_marcação(caminho_arquivo, marcacao="Título:"):
    with open(caminho_arquivo, 'r', encoding='utf-8') as arquivo:
        texto = arquivo.read()
        # Usa expressão regular para encontrar a linha que começa com a marcação
        match = re.search(f'{marcacao}(.*)', texto)
        if match:
            return match.group(1).strip()  # Retorna o título, removendo espaços extras
    return "Título não encontrado"

# Exemplo de uso
caminho_arquivo_txt = '257789.txt'
with open(caminho_arquivo_txt, 'w', encoding='utf-8') as arquivo:
    arquivo.write(texto_pdf)


titulo = extrair_titulo_por_marcação(caminho_arquivo_txt)
print("Título:", titulo)