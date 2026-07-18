import mysql.connector
from config import DATABASE


def conectar():

    return mysql.connector.connect(
        host=DATABASE["host"],
        database=DATABASE["database"],
        user=DATABASE["user"],
        password=DATABASE["password"]
    )