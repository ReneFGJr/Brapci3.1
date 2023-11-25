from mysql.connector import MySQLConnection, Error
from mysql.connector import errorcode
import datetime

sourceName = ''
URL = ''

def query(query):
    cnx = oai_mysql()
    cursor = cnx.cursor()
    cursor.execute(query)
    cursor.close


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

def next_action():
    query = "select * from brapci_bots.cron "
    query += "where cron_exec = 'python' "
    query += ""
    cnx = oai_mysql()
    cursor = cnx.cursor()
    cursor.execute(query)
    row = cursor.fetchone()
    if (len(row) > 0):
        TASK = row[1]
        try:
            TASK = TASK.decode()
        except:
            TASK = row[1]
    else:
        TASK = 'none'
    return TASK

def getNextIdentify():
    global sourceName
    global URL
    now_time = datetime.datetime.now()
    month = now_time.month
    query = f"select id_jnl,jnl_url_oai,jnl_name from brapci.source_source \n"
    query += f"where (jnl_historic = 0 and jnl_active = 1 and jnl_url_oai <> '') \n"
    query += f" and ("
    query += f"((month(`update_at`) <> {month}) or (update_at is null)) "
    query += f" and (jnl_oai_status <> '100')"
    query += " or (jnl_oai_status = '404') "
    query += ") and ("
    query += "(jnl_collection = 'JA') or (jnl_collection = 'JE')"
    query += ")"
    query += "order by update_at"

    cnx = oai_mysql()
    cursor = cnx.cursor()
    cursor.execute(query)

    row = cursor.fetchone()

    ################# Fim da Coleta
    if row == None:
        print("Nada para coletar")
        return ""

    if (len(row) > 0):
       ID = G(row[0])
       URL = G(row[1])
       name = G(row[2])
       if ID > 0:
           sourceName = name
           return ID
    return 0

def G(V):
    try:
        V = V.decode()
    except:
        V = V
    return V