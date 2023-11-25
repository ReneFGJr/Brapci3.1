from mysql.connector import MySQLConnection, Error
from mysql.connector import errorcode
import datetime
import codecs
import xmltodict

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








def identify_register(docXML,id_jnl):
    doc = docXML['OAI-PMH']
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

    print(doc)
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

def help_roboti():
    print("ROBOTi "+roboti_versao())
    print("==================")
    print("")
    print("run  - executa próxima tarefa programada")
    print("")

def test():
        v = '<Identify xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><repositoryName>ENANCIB</repositoryName><baseURL>https://conferencias.ufsc.br/index.php/enancib/2019/oai</baseURL><protocolVersion>2.0</protocolVersion><adminEmail>enancib2019@gmail.com</adminEmail><earliestDatestamp>2019-09-09T13:36:58Z</earliestDatestamp><deletedRecord>no</deletedRecord><granularity>YYYY-MM-DDThh:mm:ssZ</granularity><compression>gzip</compression><compression>deflate</compression><description><oai-identifier xmlns="http://www.openarchives.org/OAI/2.0/oai-identifier" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai-identifier      http://www.openarchives.org/OAI/2.0/oai-identifier.xsd"><scheme>oai</scheme><repositoryIdentifier>ocsbu.sites.ufsc.br</repositoryIdentifier><delimiter>:</delimiter><sampleIdentifier>oai:ocsbu.sites.ufsc.br:paper/1</sampleIdentifier></oai-identifier></description></Identify>'
        v2 = v.replace('xsi:schemaLocation="http://oai.dlib.vt.edu/OAI/metadata/toolkit http://oai.dlib.vt.edu/OAI/metadata/toolkit.xsd"','')
        try:
            doc = xmltodict.parse(v2)
        except:
            print("ERRRO NO DOC")
            print(v)
            print("=========================")
            print(v2)
        finally:
            print("OK-X")