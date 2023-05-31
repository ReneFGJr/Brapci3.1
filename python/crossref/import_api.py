#pip uninstall mysql-connector-python
#pip install mysql-connector-python
#pip install pandas
import sys, pandas as pd
import urllib
import urllib.request
import mysql.connector
import json
import doi

sys.path.insert(0, '../config/')
import database

conexao = database.conn()


print ("Importação de dados do CROSREF")

url = 'http://doi.crossref.org/getPrefixPublisher/?prefix=all'
req = urllib.request.Request(url)
try:
    with urllib.request.urlopen(req) as response:
        the_page = response.read()
        status_code = response.getcode()

        if (the_page != b'[]'):
            data=json.loads(the_page)
            for i in data:
                prefix = i['prefixes'][0]
                name = i['name']
                memberId = i['memberId']
                print(prefix,name,memberId)

                a = doi.register(prefix,name,conexao)

except Exception as e:
    print("ERROR:")
    print(e)
