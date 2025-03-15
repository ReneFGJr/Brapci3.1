import sys
import app_photo
import app_web
from colorama import init, Fore, Style

def main():
    # Verifica se o número de argumentos é suficiente
    totargv = len(sys.argv)
    if totargv < 2:
        print("Uso: python app.py PARM1 PARM2")
        return

    # Acessa os argumentos
    mod = sys.argv[1]

    # Exibe os argumentos
    if mod == 'photo':
        app_photo.main(sys.argv[2:])
    elif mod == 'web':
        app_web.main(sys.argv)
    else:
        print(f"Modulos não encontrado: {mod}")
        print("  photo - Módulo de fotos")

if __name__ == "__main__":
    print(Fore.BLUE + "APP PHOTO - Sistemas de tratamento de fundos de fotos" + Style.RESET_ALL)
    print(Fore.BLUE + "=========================================v0.25.02.15=" + Style.RESET_ALL)
    main()
