import os
import database
import mod_fundos
import mod_photo
import mod_photo_thumbnail
from datetime import datetime
from PIL import Image
from PIL.ExifTags import TAGS
import hashlib
from colorama import init, Fore, Style

def getID(ID):
    query = "SELECT * FROM imagem INNER JOIN fundo ON id_f = i_fundo WHERE id_i = %s"
    rows = database.execute(query, (ID,), fetch_one=True)
    return rows

def existe_imagem(caminho_arquivo):
    # Verifica se o caminho existe
    if os.path.exists(caminho_arquivo):
        # Verifica se é um arquivo
        if os.path.isfile(caminho_arquivo):
            return True
        else:
            return False
    else:
        return False

def obter_dados_imagem(caminho_imagem):
    """Obtém dados da imagem: nome, dimensões, tipo, tamanho, data e checksum."""
    try:
        with Image.open(caminho_imagem) as img:
            largura, altura = img.size
            tipo_imagem = img.format  # "JPEG", "PNG", etc.

        tamanho_bytes = os.path.getsize(caminho_imagem)
        data_imagem = obter_data_imagem(caminho_imagem)
        checksum = calcular_checksum(caminho_imagem)

        return os.path.basename(caminho_imagem), largura, altura, tipo_imagem, tamanho_bytes, data_imagem, checksum

    except Exception as e:
        print(f"Erro ao obter dados da imagem {caminho_imagem}: {e}")
        return None

def calcular_checksum(caminho_imagem):
    """Calcula o checksum SHA-256 da imagem."""
    sha256_hash = hashlib.sha256()
    try:
        with open(caminho_imagem, "rb") as f:
            for bloco in iter(lambda: f.read(4096), b""):
                sha256_hash.update(bloco)
        return sha256_hash.hexdigest()
    except Exception as e:
        print(f"Erro ao calcular checksum: {e}")
        return None

def imagem_existe(nome_arquivo, checksum):
    """Verifica se a imagem já está cadastrada no banco."""
    query = "SELECT COUNT(*) FROM imagem WHERE i_name = %s OR i_checksum = %s"
    rows = database.execute(query, (nome_arquivo, checksum), fetch_one=True)
    return rows[0] > 0 if rows else False

def inserir_imagem(nome_arquivo, largura, altura, tipo, tamanho, data, checksum, IDfundo):
    """Insere uma nova imagem no banco de dados."""
    if imagem_existe(nome_arquivo, checksum):
        return

    query = """
    INSERT INTO imagem (i_name, i_fundo, i_type, i_size_w, i_size_h, i_size, i_date, i_checksum)
    VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
    """
    params = (nome_arquivo, IDfundo, tipo, largura, altura, tamanho, data, checksum)
    database.execute(query, params, commit=True)
    print(f"   Imagem '{nome_arquivo}' adicionada ao banco.")

def obter_imagens(diretorio):
    """Obtém a lista de arquivos de imagem no diretório."""
    formatos_suportados = ('.jpg', '.jpeg', '.png', '.gif', '.bmp', '.tiff', '.webp')
    return [f for f in os.listdir(diretorio) if f.lower().endswith(formatos_suportados)]

def obter_data_imagem(caminho_imagem):
    """Obtém a data da imagem via metadados EXIF ou data do arquivo."""
    try:
        with Image.open(caminho_imagem) as img:
            exif_data = img._getexif()
            if exif_data:
                for tag, valor in exif_data.items():
                    tag_name = TAGS.get(tag, tag)
                    if tag_name == "DateTimeOriginal":
                        return valor  # "YYYY:MM:DD HH:MM:SS"
        timestamp =  os.path.getmtime(caminho_imagem)  # Data do arquivo
        # Converte o timestamp para um objeto datetime
        data_hora = datetime.fromtimestamp(timestamp)

        # Formata a data e hora no formato desejado
        data_hora_formatada = data_hora.strftime('%Y-%m-%d %H:%M:%S')
        return data_hora_formatada
    except Exception as e:
        print(f"Erro ao obter data da imagem {caminho_imagem}: {e}")
        return None