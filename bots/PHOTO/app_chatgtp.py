from flask import Flask, render_template
import mysql.connector

app = Flask(__name__)

DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "photo"
}

def get_images():
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT nome_imagem, objeto_detectado, identificacao FROM deteccoes")
    data = cursor.fetchall()
    conn.close()
    return data

@app.route('/')
def index():
    images = get_images()
    return render_template('index.html', images=images)

if __name__ == '__main__':
    app.run(debug=True)
