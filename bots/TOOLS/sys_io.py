import os

######################################## READFILE
def readfile(nome_arquivo):
    if len(nome_arquivo) > 255:
        print("Erro: O caminho do arquivo é muito longo.")
        print(nome_arquivo)
        return False

    try:
        # Abre o arquivo no modo de leitura
        with open(nome_arquivo, 'r', encoding='utf-8') as arquivo:
            # Lê o conteúdo do arquivo e armazena na variável
            conteudo = arquivo.read()
        return conteudo

    except FileNotFoundError:
        print(f"O arquivo '{nome_arquivo}' não foi encontrado.")
        return ""


######################################## FILENAME
def file_name(diretorio):
    try:
        # Lista todos os arquivos no diretório
        arquivos = os.listdir(diretorio)
        for arquivo in arquivos:
            if arquivo.endswith('.txt'):
                if arquivo.startswith("work_"):
                    return diretorio + arquivo

    except FileNotFoundError:
        print(f"O diretório '{diretorio}' não foi encontrado.")
    except Exception as e:
        print(f"Ocorreu um erro: {e}")
    return ""

######################################## FILEEXIST
def file_exists(file):
    if os.path.isfile(file):
        return True
    else:
        return False

######################################## GET NAME
def getNameFile(id):
    # Converte o id para string e preenche com zeros à esquerda até ter 8 caracteres
    id_str = str(id).zfill(8)

    # Divide a string em blocos de 2 caracteres
    partes = [id_str[i:i+2] for i in range(0, len(id_str), 2)]

    # Monta o caminho com a estrutura ../../public/_repository/xx/xx/xx/xx/
    caminho = "../../public/_repository/" + "/".join(partes) + "/"

    file = file_name(caminho)
    if file_exists(file):
        return file
    else:
        print("Arquivo não localizado")
    return ""

################################# Separa linhas
def separar_por_linhas(texto):
    return texto.splitlines()