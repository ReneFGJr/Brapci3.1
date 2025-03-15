from colorama import init, Fore, Style
import os, sys
import shutil
import re
from pathlib import Path
from PIL import Image
from PIL.ExifTags import TAGS
from datetime import datetime

init(autoreset=True)  # Inicializa colorama para estilização de saída

def get_photo_date(image_path):
    """ Obtém a data da foto a partir dos metadados EXIF. """
    try:
        with Image.open(image_path) as img:
            exif_data = img._getexif()
            if exif_data:
                for tag, value in exif_data.items():
                    tag_name = TAGS.get(tag, tag)
                    if tag_name == "DateTimeOriginal":
                        return value.replace(":", "-", 2)  # Converte 'YYYY:MM:DD HH:MM:SS' para 'YYYY-MM-DD HH:MM:SS'
    except Exception as e:
        print(f"Erro ao ler EXIF de {image_path}: {e}")
    return None

def get_video_date(video_path):
    """ Obtém a data do vídeo com base no nome do arquivo. """
    try:
        filename = os.path.basename(video_path)
        match = re.search(r"(\d{8})_(\d{6})", filename)
        if match:
            date_part, time_part = match.groups()
            formatted_date = datetime.strptime(date_part + time_part, "%Y%m%d%H%M%S")
            return formatted_date.strftime("%Y-%m-%d %H:%M:%S")
        else:
            print(f"Erro: Formato do nome do arquivo inválido ({filename})")
    except Exception as e:
        print(f"Erro ao obter data do vídeo {video_path}: {e}")
    return None

def get_file_date(filename: str) -> str:
    """
    Extrai a data do nome do arquivo no formato IMG-YYYYMMDD-WAxxxx.jpg e retorna a data formatada.

    :param filename: Nome do arquivo contendo a data
    :return: Data formatada como 'DD de Mês de YYYY' ou mensagem de erro
    """
    match = re.search(r'IMG-(\d{8})-WA\d+\.jpg', filename)

    if match:
        date_str = match.group(1)
        try:
            date_obj = datetime.strptime(date_str, "%Y%m%d")
            return date_obj.strftime("%Y-%m-%d")
        except ValueError:
            return "Data inválida no nome do arquivo."

    return "Formato de nome de arquivo inválido."

def organize_media(source_dir, destination_dir, extensions):
    """ Organiza arquivos de mídia (fotos) em diretórios por ano e mês. """
    source_path = Path(source_dir)
    destination_path = Path(destination_dir)
    destination_whats_path  = Path(destination_dir.replace("Fotos", "WhatsApp"))

    if not source_path.exists():
        print(Fore.RED + f"O diretório de origem {source_dir} não existe." + Style.RESET_ALL)
        return

    for ext in extensions:
        for file in source_path.glob(ext):
            if file.suffix.lower() in [".jpg", ".jpeg"]:
                file_date = get_photo_date(file)
            elif file.suffix.lower() in [".mp4"]:
                file_date = get_video_date(file)
            elif file.suffix.lower() in [".heic"]:
                file_date = file.name
                file_date = file_date[:4] + '-' + file_date[4:6] + '-' + file_date[6:8]
            else:
                continue

            if '-WA' in file.name:
                print(Fore.GREEN + f"Arquivo do WhatsApp encontrado: {file.name}, ignorando." + Style.RESET_ALL)
                file_date = get_file_date(file.name)

            if file_date:
                year = file_date[:4]
                year_month = file_date[:7]  # Obtém YYYY-MM
                target_dir = destination_path / year / year_month
                target_dir.mkdir(parents=True, exist_ok=True)  # Cria diretórios necessários
                target_path = target_dir / file.name

                if '-WA' in file.name:
                    target_dir = destination_whats_path / year / year_month
                    target_dir.mkdir(parents=True, exist_ok=True)  # Cria diretórios necessários
                    target_path = target_dir / file.name

                shutil.move(str(file), str(target_path))
                print(Fore.GREEN + f"Movido: {file} → {target_path}" + Style.RESET_ALL)
            else:
                print(Fore.YELLOW + f"Data não encontrada para {file}, ignorando." + Style.RESET_ALL)

# Diretórios de origem e destino
source_directories = [
    #"F:/Fotos/mk2/A31-Viviane",
    #"F:/Fotos/mk2/A33",
    #"F:/Fotos/mk2/2024",
    #"E:/Fotos/2021-11-29",
    #"E:/Fotos/2025-01/Photos (6)",
    #"E:/Fotos/DCIM/Camera",
    #"E:/Fotos/DCIM-2022-07/Camera"
    #"E:/Fotos/2021-11-29",
    #"F:/Fotos/mk2/A31-Viviane",
    "F:/Fotos/mk2"
]

destination_photos = "F:/Fotos/"
destination_videos = "F:/Movies/"

# Extensões suportadas
photo_extensions = ["*.jpg", "*.jpeg", "*.heic"]
video_extensions = ["*.mp4"]

# Organizando fotos
for source in source_directories:
    organize_media(source, destination_photos, photo_extensions)

# Organizando vídeos
for source in source_directories:
    organize_media(source, destination_videos, video_extensions)
