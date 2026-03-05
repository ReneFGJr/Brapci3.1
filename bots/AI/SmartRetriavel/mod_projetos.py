import os
import re
import mysql.connector
from dotenv import load_dotenv


# 🔹 Carrega variáveis do .env
load_dotenv()


def extract_id_from_url(url: str):
    """
    Extrai o ID numérico do final da URL.
    Exemplo:
    https://hdl.handle.net/20.500.11959/brapci/3405
    -> 3405
    """
    if not url:
        return None

    match = re.search(r'/(\d+)$', url)
    if match:
        return int(match.group(1))

    return None


def get_ids_by_project(project_id):
    """
    Consulta tabela brapci_ris filtrando por project_id
    e retorna lista de IDs extraídos da URL.
    """

    try:
        conn = mysql.connector.connect(host=os.getenv("DB_HOST"),
                                    user=os.getenv("DB_USER"),
                                    password=os.getenv("DB_PASSWORD"),
                                    database=os.getenv("DB_NAME"),
                                    port=int(os.getenv("DB_PORT", 3306)))


    except mysql.connector.Error as err:
        print("❌ Erro ao conectar no MySQL:")
        print(err)

    cursor = conn.cursor()

    query = """
        SELECT url
        FROM brapci_ris
        WHERE project_id = %s
    """

    cursor.execute(query, (project_id,))
    results = cursor.fetchall()

    ids = []

    for (url,) in results:
        extracted_id = extract_id_from_url(url)
        if extracted_id:
            ids.append(extracted_id)

    cursor.close()
    conn.close()

    return ids


# 🔎 Execução para teste
if __name__ == "__main__":

    projeto = 3  # alterar conforme necessário

    ids = get_ids_by_project(projeto)

    print(ids)
