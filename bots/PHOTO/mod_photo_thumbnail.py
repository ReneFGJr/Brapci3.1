from PIL import Image
import os

def criar_miniatura(caminho_imagem, diretorio_destino, largura_miniatura=120):
    """
    Cria uma miniatura da imagem redimensionada para a largura especificada (padrão: 120px),
    mantendo a proporção original da altura.

    Parâmetros:
        - caminho_imagem (str): Caminho da imagem original.
        - diretorio_destino (str): Pasta onde a miniatura será salva.
        - largura_miniatura (int): Largura da miniatura (padrão: 120px).

    Retorna:
        - str: Caminho da miniatura criada ou existente.
    """
    try:
        # Define o nome do arquivo da miniatura
        nome_arquivo = os.path.basename(caminho_imagem)
        diretorio_destino = diretorio_destino.replace("\\", "/")
        caminho_miniatura = os.path.join(diretorio_destino, f"thumb_{nome_arquivo}")
        caminho_miniatura.replace("\\", "/")

        # Verifica se a miniatura já existe
        if os.path.exists(caminho_miniatura):
            return caminho_miniatura

        # Abre a imagem original
        with Image.open(caminho_imagem) as img:
            # Obtém a largura e altura originais
            largura_original, altura_original = img.size

            # Calcula a nova altura proporcionalmente
            proporcao = largura_miniatura / float(largura_original)
            nova_altura = int(altura_original * proporcao)

            # Redimensiona a imagem mantendo a proporção
            img_miniatura = img.resize((largura_miniatura, nova_altura), Image.LANCZOS)

            # Cria o diretório de destino, se não existir
            if not os.path.exists(diretorio_destino):
                os.makedirs(diretorio_destino)

            # Salva a miniatura no diretório de destino
            img_miniatura.save(caminho_miniatura)
            print(f"   Miniatura criada: {caminho_miniatura}")
            return caminho_miniatura

    except Exception as e:
        print(f"   Erro ao criar miniatura para {caminho_imagem}: {e}")
        return None

# Exemplo de uso:
# criar_miniatura("/caminho/para/imagem.jpg", "/caminho/para/thumbnails")
