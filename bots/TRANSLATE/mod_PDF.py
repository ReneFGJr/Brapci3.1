import fitz  # PyMuPDF

def extrair_metadados_pdf(caminho_arquivo):
    # Abre o arquivo PDF
    with fitz.open(caminho_arquivo) as doc:
        # Extrai os metadados do documento
        metadados = doc.metadata
        return metadados

if __name__ == "__main__":
    caminho_arquivo_pdf = '257789.pdf'
    metadados_pdf = extrair_metadados_pdf(caminho_arquivo_pdf)
    print("Metadados do PDF:")
    for chave, valor in metadados_pdf.items():
        print(f"{chave}: {valor}")