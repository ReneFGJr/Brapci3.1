import os

def readfile(nome_arquivo):
    try:
        # Abre o arquivo no modo de leitura
        with open(nome_arquivo, 'r', encoding='utf-8') as arquivo:
            # Lê o conteúdo do arquivo e armazena na variável
            conteudo = arquivo.read()
        return conteudo
    except FileNotFoundError:
        print(f"O arquivo '{nome_arquivo}' não foi encontrado.")
        return None



def ler_arquivos_diretorio(diretorio):
    try:
        # Lista todos os arquivos no diretório
        arquivos = os.listdir(diretorio)
        print(arquivos)
    except FileNotFoundError:
        print(f"O diretório '{diretorio}' não foi encontrado.")
    except Exception as e:
        print(f"Ocorreu um erro: {e}")
    return ""

def getNameFile(id):
    # Converte o id para string e preenche com zeros à esquerda até ter 8 caracteres
    id_str = str(id).zfill(8)

    # Divide a string em blocos de 2 caracteres
    partes = [id_str[i:i+2] for i in range(0, len(id_str), 2)]

    # Monta o caminho com a estrutura ../../public/_repository/xx/xx/xx/xx/
    caminho = "../../public/_repository/" + "/".join(partes) + "/"

    file = ler_arquivos_diretorio(caminho)

    return file
