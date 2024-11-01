import database
import shutil
import os
import sys

def filename(id):
    filename = 'work_'+str(id).zfill(8)+'#00000.pdf'
    return filename

def directory(id):
    dir = str(id).zfill(8)
    diretorio = '../../public/_repository/'+dir[:2]+'/'+dir[2:4]+'/'+dir[4:6]+'/'+dir[6:8]+'/'

    # Verifique se o diretório existe
    if not os.path.isdir(diretorio):
        print(f'O diretório "{diretorio}" foi criado.')
    os.makedirs(diretorio, exist_ok=True)

    diretorio = '_repository/'+dir[:2]+'/'+dir[2:4]+'/'+dir[4:6]+'/'+dir[6:8]+'/'
    return diretorio

def copy_file(dirO,dirD):
    print(f"   De..:{dirO}")
    print(f"   Para:{dirD}")
    if os.path.isfile(dirO):  # Verifique se é um arquivo (não diretório)
        shutil.copy(dirO, dirD)
        print(f'    Arquivo {dirO}')
        print(f'    copiado para {dirD}')
    else:
        print(f"Arquivo não localizado {dirO}")

def extrair_diretorio(caminho_arquivo):
    return os.path.dirname(caminho_arquivo)

def update_rdf_data(id,name):
    qu = "update brapci_rdf.rdf_literal "
    qu += f" set n_name = '{name}' "
    qu += f" where id_n = {id} "
    database.update(qu)

def remover_arquivo(caminho_arquivo):
    try:
        os.remove(caminho_arquivo)
    except FileNotFoundError:
        print(f'O arquivo "{caminho_arquivo}" não foi encontrado.')
    except PermissionError:
        print(f'Sem permissão para remover o arquivo "{caminho_arquivo}".')
    except Exception as e:
        print(f'Erro ao remover o arquivo: {e}')

def convert(id):
    qr = "SELECT D2.d_r1, id_n, n_name FROM brapci_rdf.rdf_literal  "
    qr += "JOIN brapci_rdf.rdf_data as D1 ON D1.d_literal = id_n "
    qr += "JOIN brapci_rdf.rdf_data as D2 ON D2.d_r2 = D1.d_r1 "
    qr += f"WHERE `n_name` like '%_repository/{id}%' "

    row = database.query(qr)
    print("======================",id)
    print(qr)
    print("======================")

    print("Convert",id)
    for line in row:
        #Cria diretório
        id = line[0]
        idl = line[1]
        print("==========",id)
        dirO = '../../public/'+line[2]
        dirD = '../../public/'+directory(id)+filename(id)
        copy_file(dirO,dirD)
        remover_arquivo(dirO)

        newFilename = directory(id)+filename(id)

        update_rdf_data(idl,newFilename)

def convert_literal(id):
    qr = "SELECT D1.d_r1, id_n, n_name FROM brapci_rdf.rdf_literal  "
    qr += "JOIN brapci_rdf.rdf_data as D1 ON D1.d_literal = id_n "
    qr += f"WHERE `n_name` like '_repository/{id}%' "

    row = database.query(qr)
    print("======================",id)
    print(qr)
    print("======================")

    print("Convert",id)
    for line in row:
        #Cria diretório
        id = line[0]
        idl = line[1]
        print("==========",id,'idl',idl)
        dirO = '../../public/'+line[2]
        dirD = '../../public/'+directory(id)+filename(id)
        copy_file(dirO,dirD)
        remover_arquivo(dirO)

        newFilename = directory(id)+filename(id)

        update_rdf_data(idl,newFilename)

def convert_work():
    qr = "SELECT D1.d_r1, id_n, n_name, D1.d_r2 FROM brapci_rdf.rdf_literal  "
    qr += "JOIN brapci_rdf.rdf_data as D1 ON D1.d_literal = id_n "
    qr += f"WHERE `n_name` like '%/article_00%' "
    qr += " limit 2"

    row = database.query(qr)
    print("======================",id)
    print(qr)

    for line in row:
        idL = line[1]
        dirO = line[2]
        dirD = dirO.replace('article_00','work_00')
        dirD = dirD.replace('.pdf','#00000.pdf')
        ID = line[2]
        ID = ID[32:39]
        print(f"=============================={id}=========={idL}")
        print(f"        [{dirD}]")
        print(f"        [{dirO}]")
        print(" ID = ",ID)

        print("==",line)
        sys.exit()

        os.rename('../../public/'+dirO, '../../public/'+dirD)
        update_rdf_data(idL,dirD)
        sys.exit()

convert_work()
sys.exit()

convert_literal('0/')
convert_literal('1')
convert_literal('2')
convert_literal('3')
convert_literal('4')
convert_literal('5')
convert_literal('6')
convert_literal('7')
convert_literal('8')
convert_literal('9')

convert('0/')
convert('1')
convert('2')
convert('3')
convert('4')
convert('5')
convert('6')
convert('7')
convert('8')
convert('9')
