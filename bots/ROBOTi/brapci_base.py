from mysql.connector import MySQLConnection, Error
from mysql.connector import errorcode
from pathlib import Path
import datetime
import xmltodict
import oaipmh
import time

sourceName = ''
setSpec = []
URL = ''
def help_roboti():
    print("clear            - Zera os marcadores de coleta (recoletar)")
    print("identify         - Recupera Identificadores das publicações")
    print("listidentifiers  - Coletar registros das publicações (IDs)")

def query(query):
    cnx = oai_mysql()
    cursor = cnx.cursor()
    rs = cursor.execute(query)
    cursor.close
    return rs


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

def getNextProcess(s):
    now_time = datetime.datetime.now()
    day = now_time.day

    query = "select id_oai from brapci_oaipmh.oai_listidentify "
    query += f"where oai_status = {s} "
    query += f"order by oai_update, id_oai "

    cnx = oai_mysql()
    cursor = cnx.cursor()
    cursor.execute(query)
    row = cursor.fetchone()

    try:
        ID = row[0]
    except:
        ID = 0
    return ID

def next_action():
    now_time = datetime.datetime.now()
    day = now_time.day

    query = "select * from brapci_bots.cron "
    query += "where cron_exec = 'python' "
    query += f"and ((cron_day = 0) or (cron_day = {day})) "
    query += "order by cron_prior"

    print(query)

    cnx = oai_mysql()
    cursor = cnx.cursor()
    cursor.execute(query)
    row = cursor.fetchone()


    TASK = 'none'
    try:
        TASK = row[1]
        try:
            TASK = TASK.decode()
        except:
            TASK = row[1]
    except:
        TASK = 'none'
    return TASK


def updateOaiIdentify(ID,token):
    now_time = datetime.datetime.now()
    data = now_time.strftime("%Y-%m-%d")

    if token == '':
        qr = f"update brapci.source_source "
        qr += f" set jnl_oai_token = '', jnl_oai_last_harvesting = '{data}' "
        qr += f" where id_jnl = {ID}"
        query(qr)

    else:
        qr = f"update brapci.source_source \n"
        qr += f" set jnl_oai_token = '{token}' \n "
        qr += f" where id_jnl = {ID}"
        query(qr)


def getListIdentifier(ID):
    query = f"select jnl_url_oai, jnl_oai_token from brapci.source_source where id_jnl = {ID}"

    cnx = oai_mysql()
    cursor = cnx.cursor()
    try:
        cursor.execute(query)
        row = cursor.fetchone()
    except:
        print("ROBOTi ERROR - getListIdentifier()")
        row = []
    cursor.close()
    return row

def getNextListIdentifier():
    global sourceName
    global URL
    now_time = datetime.datetime.now()
    day = now_time.day
    month = now_time.month

    query = f"select id_jnl, jnl_url_oai, jnl_oai_last_harvesting, jnl_name "
    query += " from brapci.source_source "
    query += f" where (DAY(jnl_oai_last_harvesting) <> {day}) "
    query += f" or (MONTH(jnl_oai_last_harvesting) <> {month}) \n"
    query += f" or (jnl_oai_last_harvesting is null) \n"
    query += " order by jnl_oai_last_harvesting \n"
    query += " limit 1 "

    cnx = oai_mysql()
    cursor = cnx.cursor()
    try:
        cursor.execute(query)
        row = cursor.fetchone()
    except:
        print("ROBOTi - getNextListIdentifier()")
        row = []
    cursor.close()
    return row


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

############################################### Verifica SetSpec
def setSpecCheck(ID,set):
    qr = f"select * from brapci_oaipmh.oai_setspec where s_id = '{set}' and s_id_jnl = {ID} limit 1"
    cnx = oai_mysql()
    cursor = cnx.cursor()
    try:
        cursor.execute(qr)
        row = cursor.fetchone()
        if not row:
            qr = f"insert into brapci_oaipmh.oai_setspec (s_id, s_id_jnl) values ('{set}',{ID})"
            query(qr)
            time.sleep(1)

            qr = f"select * from brapci_oaipmh.oai_setspec where s_id = '{set}' and s_id_jnl = {ID} limit 1"
            cursor.execute(qr)
            row = cursor.fetchone()
    except:
        print("ROBOTi ERROR - getListIdentifier()")
        row = []
    cursor.close()
    return row[0]

def checkListIdentify(ID,ss,docID,date,status):
    deleted = 0
    try:
        if status != '':
            deleted = 1
    except:
        print("Problema de Status")

    date = date.replace('T',' ')
    date = date.replace('Z','')
    print(ID,ss,docID,date,status)
    qr = f"select * from brapci_oaipmh.oai_listidentify \n "
    qr += f"where oai_identifier = '{docID}' and oai_id_jnl = {ID} limit 1"
    cnx = oai_mysql()
    cursor = cnx.cursor()
    try:
        cursor.execute(qr)
        row = cursor.fetchone()
        if not row:
            qr = f"insert into brapci_oaipmh.oai_listidentify \n"
            qr += "(oai_identifier, oai_id_jnl, oai_setSpec, oai_deleted, oai_datestamp) \n"
            qr += " values "
            qr += f"('{docID}',{ID},{ss}, {deleted}, '{date}')"
            query(qr)
    except:
        print("ROBOTi ERROR - def checkListIdentify(ID,ss,docID,date):()")
        row = []
    cursor.close()
    return True

############################################################ zera Token of Journal
def zeraToken(ID):
    qr = f"update brapci.source_source set jnl_oai_token = '' where id_jnl = {ID}"
    try:
        query(qr)
    except Exception as e:
        print("============================================")
        print(e)

def processListIdentifiers(ID,docXML):
    ######################################### Read XML
    try:
        doc = xmltodict.parse(docXML)
        headers = doc['OAI-PMH']['ListIdentifiers']['header']

        try:
            for hd in headers:
                try:
                    ss = hd['setSpec'][0]
                except:
                    ss = hd['setSpec']
                docID = hd['identifier']
                date = hd['datestamp']

                try:
                    status = hd['@status']
                except:
                    status = ''
                ssID = setSpecCheck(ID,ss)

                try:
                    checkListIdentify(ID,ssID,docID,date,status)
                except Exception as e:
                    print("============================================")
                    print(e)
                    print("Erro de Registro no checkListIdentify",ID,ssID,docID,date,status)
                    print("============================================")
        except Exception as e:
            print("=========",hd)
            print("============================================")
            print(e)
            print("============================================")
            print("Erro for hd in headers",ID,ssID,docID,date)

        try:
            token = doc['OAI-PMH']['ListIdentifiers']['resumptionToken']['#text']
        except:
            token = ''

        print("TOKEN: ",token)
        updateOaiIdentify(ID,token)

        if (token == ''):
            return False
        return True

        ###################################################### Check setSpec
    except Exception as e:
        print(docXML)
        print("=processListIdentifiers=====")
        print(e)
        print("Erro ao converter o XML - processListIdentifiers")
        return False


def identify_register(id_jnl,docXML):
    ######################################### Read XML
    try:
        doc = xmltodict.parse(docXML)
    except:
        print("Erro ao converter o XML - Identify")
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
        #print(row)
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
            oaipmh.updateSource(id_jnl,'100')
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
                oaipmh.updateSource(id_jnl,'100')
            except:
                print("Brapci-base - identify_register - Erro ao atualizar a tabela Identify")
            finally:
                cursor.close()
    except:
        print("ERRO Processamento do XML")

def dbtest():
    print(".Brapci_base.dbtest.recuperando env.py")
    file_exist = Path("env.py")

    if file_exist.exists():
        print("..O arquivo 'env.py' existe!")
    else:
        print("..[ERRO] O arquivo 'env.py' NÂO existe. Copie o modelo env.py.sample para env.py e modifique-o")

    ######### Banco de Dados
    print(".Brapci_base.dbtest.conectando com o Banco MySQL")
    try:
        cnt = oai_mysql()
    except:
        print("..[ERRO] Não foi possível conectar ao banco MySQL")
    finally:
        print("..Conexão OK")

def clearMarkup():
    qr = "update brapci.source_source set jnl_oai_status = '500', update_at = null, jnl_oai_last_harvesting = '1900-01-01' where jnl_active = 1 and jnl_historic = 0"
    try:
        query(qr)
    except:
        print("Erro de atualização de registros - clean")

    qr = "update brapci.source_source set jnl_oai_status = '100', update_at = null, jnl_oai_last_harvesting = '1900-01-01' where jnl_active = 1 and jnl_historic = 1"
    try:
        query(qr)
    except:
        print("Erro de atualização de registros - clean")

def updateRegisterStatus(id,sta):
    now_time = datetime.datetime.now()
    data = now_time.strftime("%Y%m%d")
    qr = f"update brapci_oaipmh.oai_listidentify set "
    qr += f"oai_update = {data}, "
    qr += f"oai_status = {sta} "
    qr += f"where id_oai = {id} "
    query(qr)

############################################################################
def getID(ID):
    query = f"select s_id, oai_id_jnl as id_jnl from brapci_oaipmh.oai_listidentify "
    query += f"inner join brapci_oaipmh.oai_setspec ON oai_setSpec = id_s "
    query += f"where id_oai = {ID}"

    cnx = oai_mysql()
    cursor = cnx.cursor()
    try:
        cursor.execute(query)
        row = cursor.fetchone()
    except:
        print("ROBOTi ERROR - getID()")
        row = []
    cursor.close()
    return row

def getRegister(id):
    import oaipmh
    qr = f"select id_oai, oai_id_jnl, oai_identifier, jnl_url_oai "
    qr += f"from brapci_oaipmh.oai_listidentify "
    qr += f"inner join brapci.source_source ON oai_id_jnl = id_jnl "
    qr += f"where id_oai = {id} "
    try:
        cnx = oai_mysql()
        cursor = cnx.cursor()
        cursor.execute(qr)
        row = cursor.fetchone()
        cursor.close()
        cnx.close()

        try:
            identify = str(row[2],'utf-8')
        except:
            identify = row[2]

        xml = oaipmh.getRegister(identify,row[3])
        return xml
    except Exception as e:
        print(e)
        print("Next Register Load OAI")
        return 0

##########################################################################
def getNextRegister(s):
    qr = f"select id_oai from brapci_oaipmh.oai_listidentify "
    qr += f"where oai_status = {s} "
    qr += f"and oai_deleted = 0 "
    qr += "order by oai_update limit 1"
    try:
        cnx = oai_mysql()
        cursor = cnx.cursor()
        cursor.execute(qr)
        row = cursor.fetchone()
        cursor.close()
        cnx.close()
        return row[0]
    except Exception as e:
        print(e)
        print("Next Register OAI")
        return 0