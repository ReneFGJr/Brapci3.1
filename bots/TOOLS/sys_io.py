import os
import subprocess

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
def file_name_pdf(diretorio):
    try:
        # Lista todos os arquivos no diretório
        arquivos = os.listdir(diretorio)
        for arquivo in arquivos:
            if arquivo.endswith('.pdf'):
                if arquivo.startswith("work_"):
                    return diretorio + arquivo

    except FileNotFoundError:
        print(f"O diretório '{diretorio}' não foi encontrado.")
    except Exception as e:
        print(f"Ocorreu um erro: {e}")
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
    if (file_exists(file)) & (file != ''):
        return file
    else:
        fileO = file_name_pdf(caminho)
        print("=======",fileO,"[[]]")
        if file_exists(fileO):
            convertPDF4TXT(file,fileO)
            return file
        else:
            print("Arquivo não localizado -",caminho,fileO)

    return ""

################################# Convert PDF to TXT
# Executa o comando pdftotext
def convertPDF4TXT(input_pdf, output_txt):
    try:
        subprocess.run(["pdftotext", input_pdf, output_txt], check=True)
        print(f"Arquivo convertido com sucesso para {output_txt}")
    except subprocess.CalledProcessError as e:
        print("Erro ao converter o arquivo PDF para texto:", e)

################################# Separa linhas
def separar_por_linhas(texto):
    return texto.splitlines()

################################# Remove Números
def remover_numeros(texto):
    return ''.join([char for char in texto if not char.isdigit()])

################################ Remove Legendas
def remove_legendas(texto):
    textO = ''
    # Divide o texto em linhas

    linhas = separar_por_linhas(texto)

    # Remove números de cada linha
    linhasO = [remover_numeros(linha) for linha in linhas]

    # Dicionário para armazenar as ocorrências e os índices
    contagem_indices = {}
    for i, linha in enumerate(linhasO):
        if linha in contagem_indices:
            contagem_indices[linha].append(i)
        else:
            contagem_indices[linha] = [i]

    # Filtra os índices das linhas que estão duplicadas
    indices_duplicados = []
    for indices in contagem_indices.values():
        if len(indices) > 1:  # Se a linha aparece mais de uma vez
            indices_duplicados.extend(indices)

    # Remove as linhas duplicadas a partir dos índices, em ordem decrescente
    for idln in sorted(indices_duplicados, reverse=True):
        if not '.' in linhas[idln]:
            linhas.pop(idln)

    for ln in linhas:
        textO += ln + '\n'

    return textO

################################## Verifique se existe numeros
def soNumero(texto):
    return any(char.isdigit() for char in texto)