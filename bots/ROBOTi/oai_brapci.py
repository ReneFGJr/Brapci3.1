import mysql.connector
from mysql.connector import errorcode

def oai_log(status: str):
    content = status
    return f"print('{content})"

def oai_log_register(id:str, verb:str, status:str):
    cnx = oai_mysql()
    query = "insert into oai_logs "
    query += "(log_id_jnl, log_verb, log_status)"
    query += " VALUES "
    query += "(%s,%s,%s)"
    vals = [(id,verb,status)]

    cursor = cnx.cursor()
    cursor.executemany(query, vals)
    cnx.commit()

def oai_mysql():
    try:
        cnx = mysql.connector.connect(user= 'root', password= '448545ct', host= '127.0.0.1', database= 'brapci_oai', raise_on_warnings= True)
    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Something is wrong with your user name or password")
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist")
        else:
            print(err)
    return cnx