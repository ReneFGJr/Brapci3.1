#pip install mysql-connector
#pip install mysql-connector-python-rf
import mysql.connector
import json

def conn():
    connection = None
    with open('../.env') as dcjson:
        data = json.load(dcjson)

    i = data['db']
    host_name = i['host']
    user_name = i['user']
    user_password = i['password']

    try:
        connection = mysql.connector.connect(
            host=host_name,
            user=user_name,
            passwd=user_password
        )
    except Error as err:
        print(f"Error: '{err}'")

    return connection

#if conexao.is_connected():
#    cursor.close()
#    conexao.close()
#    print("Conexão ao MySQL foi encerrada")
