import mysql.connector
import database
from colorama import Fore

def dbtest():
    # Executar uma consulta
    qr = "SELECT * FROM source_source limit 10"

    resultados = database.query(qr)

    print(Fore.WHITE+"Resultados")

    for linha in resultados:
        print(Fore.WHITE+linha['id_jnl']+Fore.BLUE+linha+Fore.White)

def dbtest2():
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
        consulta = "SELECT * FROM source_source"
        cursor.execute(consulta)

        # Buscar todos os resultados
        resultados = cursor.fetchall()
        for linha in resultados:
            print(linha)

    except mysql.connector.Error as erro:
        print("Erro de Banco de Dados:", erro)

    finally:
        # Fechar o cursor e a conexão
        if conexao.is_connected():
            cursor.close()
            conexao.close()