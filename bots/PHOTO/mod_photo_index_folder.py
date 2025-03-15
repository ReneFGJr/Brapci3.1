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

def processar_fundo(IDfundo):
    """Processa todas as imagens do diretório e cadastra no banco."""
    fundo = database.execute("SELECT f_path FROM fundo WHERE id_f = %s", (IDfundo,), fetch_one=True)

    if not fundo:
        print(Fore.BLUE + f"Fundo com ID {IDfundo} não encontrado."+Style.RESET_ALL)
        return

    diretorio = fundo[0]
    print(f"Processando imagens do fundo '{diretorio}'...")

    imagens = mod_photo.obter_imagens(diretorio)

    if not imagens:
        print("Nenhuma imagem encontrada no diretório.")
        return

    for imagem in imagens:
        caminho_imagem = os.path.join(diretorio, imagem)
        dados_imagem = mod_photo.obter_dados_imagem(caminho_imagem)

        if dados_imagem:
            mod_photo.inserir_imagem(*dados_imagem, IDfundo)

            # Criar miniatura
            try:
                mod_photo_thumbnail.criar_miniatura(
                    caminho_imagem, os.path.join(diretorio, "thumbnails")
                )
            except Exception as e:
                print(f"Erro ao criar miniatura para {imagem}: {e}")

    print("Processo concluído!")

# Uso
#diretorio_imagens = "E:/Projeto/www/Brapci3.1/bots/PHOTO/IMAGES"
#IDfundo = mod_fundos.obter_id_fundo(diretorio_imagens)
#processar_fundo(IDfundo)
