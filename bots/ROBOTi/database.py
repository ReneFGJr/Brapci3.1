import mysql.connector
def query(qr):
    try:
        # Conectar ao banco de dados com charset UTF-8
        conexao = mysql.connector.connect(
            host='localhost',
            user='root',
            password='448545ct',
            database='brapci',
            charset='utf8'
        )

        # Criar um cursor
        cursor = conexao.cursor()

        # Executar uma consulta
        cursor.execute(qr)

        # Buscar todos os resultados
        resultados = cursor.fetchall()
        return resultados

    except mysql.connector.Error as erro:
        print("Erro de Banco de Dados:", erro)

    finally:
        # Fechar o cursor e a conexão
        if conexao.is_connected():
            cursor.close()
            conexao.close()