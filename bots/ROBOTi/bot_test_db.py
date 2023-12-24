import mysql.connector

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