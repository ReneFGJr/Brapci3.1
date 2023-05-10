#pip uninstall mysql-connector-python
#pip install mysql-connector-python
#pip install pandas
import sys, pandas as pd
import urllib
import urllib.request
import mysql.connector


sys.path.insert(0, '../config/')
import mysql.connector

conexao = mysql.connector.connect(
    host='localhost',
    user='root',
    password='448545ct',
    database='brapci',
    auth_plugin='mysql_native_password',
)

sql = f"SELECT id_lk, lk_doi from brapci_openaire.openaire_linkproviders where lk_status = 0"
cursor = conexao.cursor()
cursor.execute(sql)
#linha = cursor.fetchone()
results = cursor.fetchall()

for result in results:
  url = 'http://api.scholexplorer.openaire.eu/v1/linksFromPid?pid='+result[1] #.decode('utf-8')
  print(url)

  req = urllib.request.Request(url)

  try:
    with urllib.request.urlopen(req) as response:
        the_page = response.read()

        status_code = response.getcode()

        if (the_page != b'[]'):
          html = the_page.decode('utf-8')
          html = html.replace("'","´")
          sql = 'UPDATE brapci_openaire.openaire_linkproviders SET lk_status = 1, '
          sql = sql + 'lk_http = \''+format(status_code)+'\','
          sql = sql + 'lk_result = \''+str(html)+'\' WHERE id_lk = '+str(result[0])

          cursor_update = conexao.cursor()
          cursor_update.execute(sql)
          conexao.commit()
          cursor_update.close()
        else:
           sql = 'UPDATE brapci_openaire.openaire_linkproviders SET lk_http = \''+format(status_code)+'\', lk_status = 2 WHERE id_lk = '+str(result[0])
           cursor_update = conexao.cursor()
           cursor_update.execute(sql)
           conexao.commit()
           cursor_update.close()
  except Exception as e:
        print("ERROR:")
        print(e)


cursor.close()
conexao.close()

#columns = ["id_lk", "lk_doi"]
#df = pd.DataFrame(from_db, columns=columns)
