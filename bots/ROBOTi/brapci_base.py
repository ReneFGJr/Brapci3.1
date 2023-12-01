from mysql.connector import MySQLConnection, Error
from mysql.connector import errorcode
import datetime
import xmltodict
import oaipmh

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
    query += f"where \n"
    query += f"(jnl_historic = 0 and jnl_active = 1 and jnl_url_oai <> '') \n"
    query += " and \n"
    query += "((jnl_collection = 'JA') or (jnl_collection = 'JE')) \n"
    query += f" and \n"
    query += f"(((month(`update_at`) <> {month}) or (update_at is null)) and (jnl_oai_status <> '100'))"
    query += "order by update_at"

    cnx = oai_mysql()
    cursor = cnx.cursor()
    cursor.execute(query)

    row = cursor.fetchone()

    ################# Fim da Coleta
    if row == None:
        print("getNextIdentify - Nada para coletar")
        return 0

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

def identify_register(id_jnl,docXML):
    ######################################### Read XML
    try:
        doc = xmltodict.parse(docXML)
    except:
        print("Erro ao converter o XML")
        return False

    doc = doc['OAI-PMH']
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
        print(row)
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
            oaipmh.updateSource(ID,'100')
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
                oaipmh.updateSource(ID,'100')
            except:
                print("Brapci-base - identify_register - Erro ao atualizar a tabela Identify")
            finally:
                cursor.close()
    except:
        print("ERRO Processamento do XML")