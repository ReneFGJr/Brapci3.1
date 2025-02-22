import os
import sys
import subprocess
import database
import mod_rdf
import mod_convert_repository

######################################## READFILE
def readfile(nome_arquivo):
    dirT = '../../public/'
    if len(nome_arquivo) > 255:
        print("Erro: O caminho do arquivo é muito longo.")
        return False

    try:
        with open(dirT + nome_arquivo, 'r', encoding='utf-8', errors='replace') as arquivo:
            conteudo = arquivo.read()
        return conteudo
    except FileNotFoundError:
        print(f"O arquivo '{dirT + nome_arquivo}' não foi encontrado. [404-3]")
        return ""
    except UnicodeDecodeError as e:
        print(f"Erro de decodificação: {e}")
        return ""

######################################## FILENAME
def file_name_pdf(diretorio,id):
    try:
        # Lista todos os arquivos no diretório
        arquivos = os.listdir(diretorio)
        for arquivo in arquivos:
            if arquivo.endswith('.pdf'):
                if arquivo.startswith("work_"):
                    return diretorio + arquivo

    except FileNotFoundError:
        print(f"O diretório '{diretorio}' não foi encontrado. [404-1]")
        #****************************************** Recupera pelo ID ******
        diretorio = recover_file_id(id)

    except Exception as e:
        print(f"Ocorreu um erro: {e}")
    return ""
######################################## RECOVER ONTOLOGY
def recover_file_id(id):
    print(id)
    data = mod_rdf.recover(id,'hasFileStorage')

    for line in data:
        print("======>",line)
        mod_convert_repository.directory(line)
    print(data)
    sys.exit()
######################################## FILENAME
def file_name(diretorio):
    dirT = '../../public/'
    try:
        # Lista todos os arquivos no diretório
        arquivos = os.listdir(dirT+diretorio)
        for arquivo in arquivos:
            print(" = = = = = = = = = FILE",arquivo)
            if arquivo.endswith('.txt'):
                if arquivo.startswith("work_"):
                    return diretorio + arquivo

    except FileNotFoundError:
        print(f"O diretório '{dirT+diretorio}' não foi encontrado. [404-2]")
    except Exception as e:
        print(f"Ocorreu um erro: {e}")
    return ""

######################################## FILEEXIST
def file_exists(file):
    if os.path.isfile(file):
        return True
    else:
        return False
######################################## GET TXT
def getNameFileTXT(fileO):
    fileTxt = fileO.replace('.pdf','.txt')
    if not file_exists(fileTxt):
        print("Converter para TXT")
        convertPDF4TXT(fileO,fileTxt)
    return fileTxt

######################################## GET NAME
def getNameFile(id,loop=True):
    files = mod_rdf.recover(id,'hasFileStorage')
    print("Files",files)
    for line in files:
        idR = line
        data = mod_rdf.le(idR)

        if data['data']== []:
            idN = data['concept'][0][3]
            dirT = '../../public/'
            fileO = data['concept'][0][1]
            # MODELO ANTIGO
            direD = mod_convert_repository.directory(idR)

            fileD = mod_convert_repository.filename(idR)
            fileD = direD + fileD

            # Existe original
            if not file_exists(dirT+fileD):
                if file_exists(dirT+fileO):
                    print("Transferindo arquivos")
                    mod_convert_repository.copy_file(dirT+fileO,dirT+fileD)
                    mod_convert_repository.update_rdf_data(idN,fileD)
                else:
                    print(f"Arquivo {fileO} não existe")
                    sys.exit()
        else:
            fileD = data['concept'][0][1]
            print("=X+X=",fileD)
    return fileD

def filename(id):
    filename = 'work_'+str(id).zfill(8)+'#00000.pdf'
    return filename

def getNameFileX(id,loop=True):
    # Converte o id para string e preenche com zeros à esquerda até ter 8 caracteres
    id_str = str(id).zfill(8)

    # Divide a string em blocos de 2 caracteres
    partes = [id_str[i:i+2] for i in range(0, len(id_str), 2)]

    # Monta o caminho com a estrutura ../../public/_repository/xx/xx/xx/xx/
    caminho = "_repository/" + "/".join(partes) + "/"

    file = file_name(caminho) + filename(id)
    file = file.replace('.pdf','.txt')
    return file


def recoverHasFile(id):
    qr = "select d_r2 from brapci_rdf.rdf_data "
    qr += f" where d_r1 = {id} and d_p = 80"
    row = database.query(qr)
    print(row)
    for line in row:
        return line[0]
    print("########## ERRO - ID not found")
    sys.exit()


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