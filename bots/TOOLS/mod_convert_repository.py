import database
import shutil
import os

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
    return diretorio

def copy_file(dirO,dirD):
    if os.path.isfile(dirO):  # Verifique se é um arquivo (não diretório)
        shutil.copy(dirO, dirD)
        print(f'Arquivo {dirO} copiado para {dirD}')

def extrair_diretorio(caminho_arquivo):
    return os.path.dirname(caminho_arquivo)

def convert():
    qr = "SELECT D2.d_r1, id_n, n_name FROM brapci_rdf.rdf_literal  "
    qr += "JOIN brapci_rdf.rdf_data as D1 ON D1.d_literal = id_n "
    qr += "JOIN brapci_rdf.rdf_data as D2 ON D2.d_r2 = D1.d_r1 "
    qr += "WHERE `n_name` like '_repository/1%' "
    qr += "limit 1 "

    row = database.query(qr)

    print("Convert")
    for line in row:
        print(line)
        #Cria diretório
        id = line[0]
        dirO = '../../public/'+extrair_diretorio(line[2])
        dirD = directory(id)+filename(id)
        copy_file(dirO,dirD)

convert()