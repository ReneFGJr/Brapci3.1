import mysql.connector
import env
def query(qr):
    resultados = []
    try:
        # Conectar ao banco de dados com charset UTF-8
        config = env.db()
        conexao = mysql.connector.connect(**config)

        # Criar um cursor
        cursor = conexao.cursor()

        # Executar uma consulta
        cursor.execute(qr)

        # Buscar todos os resultados
        resultados = cursor.fetchall()


    except mysql.connector.Error as erro:
        print("Erro de Banco de Dados:", erro)
        print(qr)
    finally:
        # Fechar o cursor e a conexão
        if conexao.is_connected():
            cursor.close()
            conexao.close()
        else:
            print("Conexão já estava finalizadas")

    return resultados
def update(qr):
    return insert(qr)

def insert(qr):
    try:
        config = env.db()
        conexao = mysql.connector.connect(**config)

        # Criar um cursor
        cursor = conexao.cursor()

        # Executar uma consulta
        cursor.execute(qr)

    except mysql.connector.Error as erro:
        print("Erro de Banco de Dados:", erro)

    finally:
        # Fechar o cursor e a conexão
        conexao.commit()

        if conexao.is_connected():
            cursor.close()
            conexao.close()