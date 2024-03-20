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

    qr = "SHOW DATABASES;"
    row = database.query(qr)

    for data in row:
        print(data[0])

        cmd = "mysqldump -u "+config['user']+ " -p "+config['password']+" "+data[0]+" > /backup/"+data[0]+"_"+date+".sql"
        cmd = "mysqldump "+data[0]+" > /backup/"+data[0]+"_20240101.sql"

        result = subprocess.run([sys.executable, "-c", cmd], capture_output=True, text=True, timeout=60)
        print("stdout:", result.stdout)
        print("stderr:", result.stderr)

main()