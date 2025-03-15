import mysql.connector


# Configuração do Banco de Dados MySQL
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "photo"
}

# Criando conexão com o banco de dados
def create_database():
    conn = mysql.connector.connect(
        host=DB_CONFIG["host"],
        user=DB_CONFIG["user"],
        password=DB_CONFIG["password"]
    )
    cursor = conn.cursor()
    cursor.execute("CREATE DATABASE IF NOT EXISTS detecao_imagens")
    conn.close()

def create_table():
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    cursor.execute("""
        CREATE TABLE IF NOT EXISTS deteccoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome_imagem VARCHAR(255),
            objeto_detectado VARCHAR(255),
            identificacao VARCHAR(255)
        )
    """)
    conn.close()
def execute(query, params=None, fetch_one=False, commit=False):
    """
    Executa uma consulta SQL no banco de dados.

    Parâmetros:
        - query (str): A query SQL a ser executada.
        - params (tuple): Parâmetros da query para evitar SQL Injection.
        - fetch_one (bool): Se True, retorna apenas um resultado; caso contrário, retorna todos.
        - commit (bool): Se True, confirma a operação (para INSERT, UPDATE, DELETE).

    Retorno:
        - Se for uma consulta SELECT: Retorna os resultados.
        - Se for um INSERT/UPDATE/DELETE: Retorna None.
    """
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()

    try:
        cursor.execute(query, params or ())

        if commit:
            conn.commit()
            return None  # Para operações de escrita, não há necessidade de retorno

        if query.strip().lower().startswith("select"):
            return cursor.fetchone() if fetch_one else cursor.fetchall()

    except mysql.connector.Error as e:
        print(f"Erro no banco de dados: {e}")
    finally:
        cursor.close()
        conn.close()

    return None

# Função para salvar os dados no banco de dados
def save_to_database(image_name, detected_objects):
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()

    for obj in detected_objects:
        cursor.execute("INSERT INTO deteccoes (nome_imagem, objeto_detectado, identificacao) VALUES (%s, %s, %s)",
                       (image_name, obj["label"], obj["id"]))

    conn.commit()
    conn.close()