#pip install mysql-connector
#pip install mysql-connector-python-rf
import mysql.connector

def conn(host_name, user_name, user_password):
    connection = None
    try:
        connection = mysql.connector.connect(
            host=host_name,
            user=user_name,
            passwd=user_password
        )
        print("MySQL Database connection successful")
    except Error as err:
        print(f"Error: '{err}'")

    return connection

conexao = mysql.connector.connect(
    host='localhost',
    user='root',
    password='448545ct',
    database='brapci',
)

if conexao.is_connected():
    db_info = conexao.get_server_info()
    print("Conectado ao servidor MySQL versão ",db_info)
    cursor = conexao.cursor()
    cursor.execute("select database();")
    linha = cursor.fetchone()
    print("Conectado ao banco de dados ",linha)

#if conexao.is_connected():
#    cursor.close()
#    conexao.close()
#    print("Conexão ao MySQL foi encerrada")
