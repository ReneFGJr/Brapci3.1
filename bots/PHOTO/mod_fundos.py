import mod_photo_index_folder
import database
import sys

def processar_fundo(IDfundo):
    print("======================")
    """Processa todas as imagens do diretório e cadastra no banco."""
    fundo = database.execute("SELECT id_f, f_path FROM fundo WHERE id_f = %s", (IDfundo,), fetch_one=True)

    if not fundo:
        print(f"Fundo com ID {IDfundo} não encontrado.")
        return

    diretorio = fundo[1]
    IDfundo = fundo[0]
    print(f"Processando imagens do fundo '{diretorio} ({IDfundo})'...")
    mod_photo_index_folder.processar_fundo(IDfundo)
    return

def listar_imagens(IDfundo):
    """Lista todas as imagens cadastradas no banco."""
    query = "SELECT * FROM imagem WHERE i_fundo = %s"
    rows = database.execute(query, (IDfundo,))

    if not rows:
        print("Nenhuma imagem cadastrada.")
        return

    print(f"Imagens do fundo {IDfundo}:")
    for row in rows:
        # Supondo que row[6] contenha o valor em bytes
        bytes_value = row[6]

        # Convertendo bytes para megabytes
        megabytes_value = bytes_value / (1024 * 1024)

        print(f"  {row[0]} - {row[1]}- {megabytes_value:.2f} MB - {row[3]}- {row[4]}x{row[5]}")

def getFundos():
    """Lista todos os fundos cadastrados no banco."""
    query = "SELECT * FROM fundo"
    rows = database.execute(query)
    return rows

def getFundo(fundo):
    """Obtém os dados de um fundo."""
    query = "SELECT * FROM fundo WHERE id_f = %s"
    rows = database.execute(query, (fundo,))

    query = "SELECT * FROM imagem WHERE i_fundo = %s order by i_name"
    photos = database.execute(query, (fundo,))
    data = [rows,photos]
    return data

def listar_fundos():
    """Lista todos os fundos cadastrados no banco."""
    query = "SELECT * FROM fundo"
    rows = database.execute(query)

    if not rows:
        print("Nenhum fundo cadastrado.")
        return

    print("Fundos cadastrados:")
    for row in rows:
        print(f"  {row[0]} - {row[1]}")

def obter_id_fundo(name):
    name = name.replace("\\","/") # Ajusta o caminho para o padrão do banco
    qr = "select * from fundo where f_path = '" + name + "'"
    rows = database.execute(qr)

    if rows == []:
        qi = "insert into fundo (f_name,f_path) values ('" + name + "','" + name + "')"
        database.execute(qi)
        rows = database.execute(qr)
        print("#### NOVO FUNDO ####")
    return rows[0][0]