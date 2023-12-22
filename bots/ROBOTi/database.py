from mysql.connector import MySQLConnection, Error
from mysql.connector import errorcode

def oai_mysql():
    import env
    dbconfig = env.db()

    try:
        cnx = MySQLConnection(**dbconfig)
    except MySQLConnection.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Something is wrong with your user name or password")
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist")
        else:
            print(err)
    return cnx

def query(sql):
    cnx = oai_mysql()
    cursor = cnx.cursor()
    try:
        cursor.execute(query)
        row = cursor.fetchall()
    except Exception as e:
        print("MySQL Error",e)
        row = []
    cursor.close()
    cnx.close()
    return row