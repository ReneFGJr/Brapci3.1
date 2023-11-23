import mysql.connector
from mysql.connector import errorcode
import urllib.request
import xml.etree.ElementTree as ET

import sys
#sys.path.insert(0, '../')


import env
try:
  cnx = mysql.connector.connect(  user= env.config['user'], password=env.config['password'], host= env.config['host'], database= env.config['database'], raise_on_warnings= True)
except mysql.connector.Error as err:
  if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
    print("Something is wrong with your user name or password")
  elif err.errno == errorcode.ER_BAD_DB_ERROR:
    print("Database does not exist")
  else:
    print(err)
else:

    sql_select_Query = "select jnl_name, jnl_oai_last_harvesting, "
    sql_select_Query += "jnl_url_oai, jnl_oai_token "
    sql_select_Query += "from source_source "
    sql_select_Query += "where jnl_historic = 0 and jnl_active = 1 "
    sql_select_Query += "and jnl_url_oai <> '' "
    sql_select_Query += "order by jnl_oai_last_harvesting"
    cursor = cnx.cursor()
    cursor.execute(sql_select_Query)
    #records = cursor.fetchall()
    oai = cursor.fetchone()

    #url = str(oai[0], 'utf-8')
    url = str(oai[2])
    token = str(oai[3])

    if (token == ''):
        url = 'https://seer.ufrgs.br/emquestao/oai'
        url = url + '?verb=ListIdentifiers&metadataPrefix=oai_dc'
    else:
        print("TOKEN")
        print("OK")
    cnx.close()

    #####Abre via navegador
    xml = urllib.request.urlopen(url).read()
    print("=================================#2")

    tree = ET.fromstring(xml)

    lst = tree.findall('header')
    print(lst)
    #print(tree)
    print(lst[:100])