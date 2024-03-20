import database
import subprocess
import sys
import env
import datetime

def main():
    config = env.db()
    #print(config)
    #return 0
    date = datetime.date.today()
    date = date.strftime('%Y-%m-%d')

    qr = "SHOW DATABASES;"
    row = database.query(qr)

    for data in row:
        print("============"+data[0])
        db = data[0]

        cmd = f"mysqldump {db} > /backup/{db}_{date}.sql"

        print(cmd)
        result = subprocess.run([sys.executable, "-c", cmd], capture_output=True, shell=True, text=True, timeout=600)
        print("stdout:", result.stdout)
        print("stderr:", result.stderr)

main()