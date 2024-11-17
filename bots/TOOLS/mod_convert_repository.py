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

    print(diretorio)

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
