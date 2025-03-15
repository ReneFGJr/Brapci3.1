from colorama import init, Fore, Style
import app_photo_fundo
import mod_fundos

def main(argv: list):

    print(Fore.BLUE + "MOD: Fundos de Fotos" + Style.RESET_ALL)

    if len(argv) == 0:
        help()
        return

    if argv[0] == "create":
        if len(argv) < 2:
            help()
            return
        else:
            mod_fundos.obter_id_fundo(argv[1])
            return
    elif argv[0] == "help":
        help()
        return
    elif argv[0] == "list":
        if (len(argv) > 1):
            mod_fundos.listar_imagens(argv[1])
        else:
            mod_fundos.listar_fundos()
        return
    elif argv[0] == "process":
        mod_fundos.processar_fundo(argv[1])
        return
    else:
        help()
        return
def help():
        print(Fore.GREEN +"Comandos disponíveis para 'fundo':")
        print("   create - Cria um novo fundo a partir do diretório especificado.")
        print("   list - Lista os fundos existentes com seu #ID.")
        print("   list $ID- Lista imagens de um fundos #ID.")
        print("   process #ID - Indexa fotos de um fundo #ID.")
        print(Style.RESET_ALL)
        return