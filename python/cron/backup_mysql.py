#pip uninstall mysql-connector-python
#pip install mysql-connector-python
#pip install pandas
import sys, pandas as pd
import mysql.connector

sys.path.insert(0, '../config/')
import database

conexao = database.conn()

sql = f"SHOW DATABASES"
cursor = conexao.cursor()
cursor.execute(sql)
results = cursor.fetchall()
cursor.close()
print(results)

sx = ''
sx = 'echo "Brapci"\n'
for db in results:
    sx = sx + 'mysqldump '+db[0]+" /home/brapci/backup/"+db[0]+".sql\n"
sx = sx + 'echo "Fim do Backup"'
sx = sx + "\n"
sx = sx + "\n"
sx = sx + 'echo "COPIANDO PARA A REDE"\n'
sx = sx + 'cp /home/brapci/backup/*.sql /home/brapci/rede/pluto/Backup-SQL/."\n'
print(sx)