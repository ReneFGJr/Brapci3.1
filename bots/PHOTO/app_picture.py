import mod_photo_metadata
import mod_photo
from colorama import init, Fore, Style
import app_photo_fundo
import app_picture

def main(argv: list):
    print(Fore.BLUE + "MOD: Processamento de Imagens" + Style.RESET_ALL)
    ID = argv[0]
    if len(argv) == 0:
        print(Fore.RED + "Nenhum argumento fornecido." + Style.RESET_ALL)
        help()
        return
    if ID == "metadata":
        metadata(argv[1])
        return
    else:
        help()
        return

def help():
    print(Fore.GREEN +"Comandos disponíveis para 'image':")
    print("   metadata #ID - Dados da imagem")
    print("   help - Ajuda sobre os comandos disponíveis" + Style.RESET_ALL)
    return

def metadata(ID):
    metaData = mod_photo.getID(ID)
    if len(metaData) == 0:
        print(Fore.RED + "Nenhum dado encontrado." + Style.RESET_ALL)
        return

    print(Fore.GREEN + "Dados encontrados." + Style.RESET_ALL)
    size = metaData[6]/1024/1024
    size = round(size, 2)
    size = str(size) + "MB"
    print("ID: ", metaData[0])
    print("Nome: ", metaData[1])
    print("Fundo: ", metaData[11])
    print("Tipo: ", metaData[3])
    print("Size: ", str(metaData[4])+'x'+str(metaData[5]))
    print("Tamanho: ", size)
    print("Data: ", metaData[7])
    print("Checksum: ", metaData[8])
    print("========================================")

    path = metaData[11] + '/'+metaData[1]
    if mod_photo.existe_imagem(path):
        metaData = mod_photo_metadata.get_metadata(path)
        print(metaData)
        for tag, value in metaData.items():
            print(Fore.WHITE, tag, ":", Fore.GREEN,value, Style.RESET_ALL)
    else:
        print(Fore.RED, "Arquivo não encontrado:",path,Style.RESET_ALL)
