import mysql.connector
from mysql.connector import errorcode
import urllib.request
import xml.etree.ElementTree as ET

import sys
sys.path.insert(0, '../')

import env

try:
  cnx = mysql.connector.connect(  user= 'root', password= '448545ct', host= '127.0.0.1', database= 'find', raise_on_warnings= True)
except mysql.connector.Error as err:
  if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
    print("Something is wrong with your user name or password")
  elif err.errno == errorcode.ER_BAD_DB_ERROR:
    print("Database does not exist")
  else:
    print(err)
else:

    sql_select_Query = "select * from oai_source order by sr_update"
    cursor = cnx.cursor()
    cursor.execute(sql_select_Query)
    #records = cursor.fetchall()
    oai = cursor.fetchone()
    url = str(oai[2], 'utf-8')
    url = url + '?verb=ListRecords&resumptionToken=marcxml/0'

    print(url)
    print("=================================")
    cnx.close()

    #####Abre via navegador
    xml = urllib.request.urlopen(url).read()
    print(xml)

    tree = ET.fromstring(xml)

    lst = tree.findall('datafield')
    print(lst[:100])