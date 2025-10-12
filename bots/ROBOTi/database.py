import mysql.connector
import env

def update2(table, data, where=None, params=None):
    try:
        config = env.db()
        conexao = mysql.connector.connect(**config)

        # Criar um cursor
        cursor = conexao.cursor()

        # Construir a consulta SQL dinamicamente
        set_clause = ", ".join([f"{key} = %s" for key in data.keys()])
        sql = f"UPDATE {table} SET {set_clause}"
        if where:
            sql += f" WHERE {where}"

        # Preparar os valores para a consulta
        values = list(data.values())
        if params:
            values.extend(params)

        # Executar a consulta
        cursor.execute(sql, values)
        conexao.commit()

    except mysql.connector.Error as erro:
        print("Erro de Banco de Dados #30:", erro)
        print(sql)

    finally:
        # Fechar o cursor e a conexão
        if conexao.is_connected():
            cursor.close()
            conexao.close()
def query(qr, params=None):
    resultados = []
    try:
        # Conectar ao banco de dados com charset UTF-8
        config = env.db()
        conexao = mysql.connector.connect(**config)

        # Criar um cursor
        cursor = conexao.cursor()

        # Executar uma consulta
        if params:
            cursor.execute(qr, params)
        else:
            cursor.execute(qr)


        # Buscar todos os resultados
        resultados = cursor.fetchall()


    except mysql.connector.Error as erro:
        print("Erro de Banco de Dados #31:", erro)
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
        conexao.commit()

    except mysql.connector.Error as erro:
        print("Erro de Banco de Dados #32:", erro)
        print(qr)

    finally:
        # Fechar o cursor e a conexão

        if conexao.is_connected():
            cursor.close()
            conexao.close()