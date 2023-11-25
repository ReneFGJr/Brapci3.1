from mysql.connector import MySQLConnection, Error
from mysql.connector import errorcode
import datetime

def roboti_versao():
    return "v0.23.11.24"

def updateIDENTIFY(ID):
    update = datetime.datetime.now()
    query = f"update brapci.source_source set update_at = '{update}' where id_jnl = {ID}"
    cnx = oai_mysql()
    cursor = cnx.cursor()
    cursor.execute(query)
    cursor.close()
    cnx.commit()

def getIDENTIFY():
    now_time = datetime.datetime.now()
    month = now_time.month
    query = f"select id_jnl,jnl_url_oai,jnl_name from brapci.source_source \n"
    query += f"where (jnl_historic = 0 and jnl_active = 1 and jnl_url_oai <> '') \n"
    query += f" and ((month(`update_at`) <> {month}) or (update_at is null)) "
    query += f" and (jnl_oai_status <> '100') "
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
       ID = row[0]
       URL = row[1]
       if ID > 0:
           import oai_identify
           updateIDENTIFY(ID)
           oai_identify.harvesting(ID,URL)
    return 0

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
    else:
        TASK = 'none'
    return TASK

def identify_register(doc):
    id_jnl = doc['id_jnl']
    repositoryName = doc['Identify']['repositoryName']
    baseURL = doc['Identify']['baseURL']
    protocolVersion = doc['Identify']['protocolVersion']
    adminEmail = doc['Identify']['adminEmail']
    earliestDatestamp = doc['Identify']['earliestDatestamp']
    deletedRecord = doc['Identify']['deletedRecord']
    delimiter = doc['Identify']['description'][0]['oai-identifier']['delimiter']
    sampleIdentifier = doc['Identify']['description'][0]['oai-identifier']['sampleIdentifier']
    tool = doc['Identify']['description'][1]['toolkit']['title']
    tool_version = doc['Identify']['description'][1]['toolkit']['version']
    scheme = doc['Identify']['description'][0]['oai-identifier']['scheme']
    update_time = datetime.datetime.now()

    query = "select * from brapci_oaipmh.oai_identify where hv_id_jnl = "+str(id_jnl)
    try:
        cnx = oai_mysql()
        cursor = cnx.cursor()
        cursor.execute(query)
        row = cursor.fetchone()
        if (row == None):
            print("NOVO")
            query = "insert into oai_identify "
            query += "("
            query += "hv_id_jnl, hv_repositoryName, hv_baseURL, hv_protocolVersion"
            query += ",hv_adminEmail, hv_earliestDatestamp, hv_deletedRecord"
            query += ",hv_scheme, hv_tool_source, hv_tool_version, hv_sampleIdentifier"
            query += ",hv_delimiter, hv_updated"
            query += ")"
            query += " VALUES "
            query += "("
            query += "%s,%s,%s,%s"
            query += ",%s,%s,%s"
            query += ",%s,%s,%s,%s"
            query += ",%s,%s"
            query += ")"
            vals = [(id_jnl,repositoryName,baseURL,protocolVersion,adminEmail,earliestDatestamp,deletedRecord,scheme,tool,tool_version,sampleIdentifier,delimiter,update_time)]

            cursor = cnx.cursor()
            cursor.executemany(query, vals)
            cnx.commit()

        else:
            print("UPDATE")

            query = "update oai_identify set "
            query += f"hv_repositoryName = '{repositoryName}' , "
            query += f"hv_baseURL = '{baseURL}' , "
            query += f"hv_protocolVersion = '{protocolVersion}' , "
            query += f"hv_adminEmail = '{adminEmail}' , "
            query += f"hv_earliestDatestamp = '{earliestDatestamp}' , "
            query += f"hv_deletedRecord = '{deletedRecord}' , "
            query += f"hv_tool_source = '{tool}' , "
            query += f"hv_tool_version = '{tool_version}' , "
            query += f"hv_sampleIdentifier = '{sampleIdentifier}' , "
            query += f"hv_delimiter = '{delimiter}' , "
            query += f"hv_updated = '{update_time}' "
            query += f"where hv_id_jnl = {id_jnl}"

            cursor = cnx.cursor()
            try:
                cursor.execute(query)
            except:
                print("Erro ao atualizar a tabela Identify")
            finally:
                cursor.close()
                cnx.commit()


    except Error as e:
        print("Erro de consulta na Tabela Identify")
        print(query)
        print(e)
    finally:
        cnx.commit()

def jnl_oai_status(ID:str,status:str):
    query = f"update brapci.source_source set jnl_oai_status = {status} where id_jnl = {ID}"

    cnx = oai_mysql()
    cursor = cnx.cursor()
    cursor.execute(query)
    cnx.commit()
    return True

def oai_log_register(id:str, verb:str, status:str):
    cnx = oai_mysql()
    query = "insert into brapci_oaipmh.oai_logs "
    query += "(log_id_jnl, log_verb, log_status)"
    query += " VALUES "
    query += "(%s,%s,%s)"
    vals = [(id,verb,status)]

    cursor = cnx.cursor()
    cursor.executemany(query, vals)
    cnx.commit()

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

def help_roboti():
    print("ROBOTi "+roboti_versao())
    print("==================")
    print("")
    print("run  - executa próxima tarefa programada")
    print("")