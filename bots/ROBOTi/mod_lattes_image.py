import requests
from PIL import Image
from io import BytesIO

# URL da imagem
url = "https://servicosweb.cnpq.br/wspessoa/servletrecuperafoto?tipo=1&id=K4765743Y2"

# Fazer o download da imagem
response = requests.get(url)
if response.status_code == 200:
    # Abrir a imagem usando BytesIO
    image = Image.open(BytesIO(response.content))

    # Redimensionar a imagem para 600px de largura mantendo a proporção
    width, height = image.size
    new_height = int((600 / width) * height)
    resized_image = image.resize((600, new_height), Image.LANCZOS)  # Substitua Image.ANTIALIAS por Image.LANCZOS

    # Salvar a imagem redimensionada
    resized_image.save("__IMG.jpg")
    print("Imagem redimensionada e salva com sucesso!")
else:
    print("Falha ao baixar a imagem.")
