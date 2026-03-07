import mysql.connector
import subprocess
import os, sys
from dotenv import load_dotenv

# =========================
# CARREGA .ENV
# =========================
load_dotenv("../../.env")

database = 'brapci_rdf'
version = '2026-02-15'

DB_HOST = os.getenv("database.default.hostname")
DB_USER = os.getenv("database.default.username")
DB_PASS = os.getenv("database.default.password")

BACKUP_FILE = f"{database}_{version}.sql"

print("Conectando ao MySQL...")

# =========================
# CONEXÃO
# =========================
conn = mysql.connector.connect(
    host=DB_HOST,
    user=DB_USER,
    password=DB_PASS,
    database=database
)

cursor = conn.cursor()

# =========================
# DESATIVA FK
# =========================
cursor.execute("SET FOREIGN_KEY_CHECKS = 0")

# =========================
# LISTA TABELAS
# =========================
cursor.execute("SHOW TABLES")
tables = cursor.fetchall()

print(f"{len(tables)} tabelas encontradas")

# =========================
# REMOVE TABELAS
# =========================
for table in tables:
    print("Removendo:", table[0])
    sys.exit()
    cursor.execute(f"DROP TABLE IF EXISTS `{table[0]}`")

conn.commit()

cursor.execute("SET FOREIGN_KEY_CHECKS = 1")

cursor.close()
conn.close()

print("Banco limpo.")

# =========================
# RESTORE
# =========================
print("Restaurando backup...")

cmd = f"mysql -h {DB_HOST} -u {DB_USER} -p{DB_PASS} {DB_NAME} < {BACKUP_FILE}"

subprocess.run(cmd, shell=True)

print("Restore concluído.")