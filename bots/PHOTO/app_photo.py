
from colorama import init, Fore, Style
import app_photo_fundo
import app_picture

def main(argv: list):

    if len(argv) == 0:
        print(Fore.RED + "Nenhum argumento fornecido." + Style.RESET_ALL)
        help()
        return
    ################################ FUNDO ###############################
    if argv[0] == "fundo":
        app_photo_fundo.main(argv[1:])
        return
    elif argv[0] == "image":
        app_picture.main(argv[1:])
        return

    ################################ HELP ################################

    else:
        help()
        return
def help():
    print(Fore.GREEN +"Comandos disponíveis para 'photo':")
    print("   fundo - Módulo de fundos")
    print("   image - Módulo de imagens")
    print("   help - Ajuda sobre os comandos disponíveis" + Style.RESET_ALL)
    return