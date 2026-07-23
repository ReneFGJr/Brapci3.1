import mysql.connector
from config import DB


def conectar():

    return mysql.connector.connect(
        host=DB["host"],
        port=DB["port"],
        database=DB["database"],
        user=DB["user"],
        password=DB["password"]
    )