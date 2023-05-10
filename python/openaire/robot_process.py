#pip install pandas
import sys, pandas as pd
import urllib
import urllib.request

sys.path.insert(0, '../config/')
import mysql.connector

conexao = mysql.connector.connect(
    host='localhost',
    user='root',
    password='448545ct',
    database='brapci',
)

sql = f"SELECT id_lk, lk_doi from brapci_openaire.openaire_linkproviders where lk_status = 0"
cursor = conexao.cursor()
cursor.execute(sql)
#linha = cursor.fetchone()
results = cursor.fetchall()

cursor.close()
conexao.close()

for result in results:
  url = 'https://api.datacite.org/dois/'+result[1].decode('utf-8')
  print(url)

  req = urllib.request.Request(url)
  with urllib.request.urlopen(req) as response:
        the_page = response.read()
  print(the_page)

  break


#columns = ["id_lk", "lk_doi"]
#df = pd.DataFrame(from_db, columns=columns)
