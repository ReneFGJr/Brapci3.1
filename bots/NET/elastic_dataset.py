import database
import mod_reverse_index
import sys

def fetch_unique_authors():
    qr = "SELECT id_ds, AUTHORS FROM brapci_elastic.dataset"
    rows = database.query(qr)

    unique_authors = set()

    for row in rows:
        raw = row[1] or ""
        # opcional: limpa marcações como "(Org.)"
        raw = raw.replace('(Org.)', '')
        # separa em vários se houver ';', senão usa a própria string
        parts = raw.split(';') if ';' in raw else [raw]
        # normaliza e adiciona ao set
        for author in parts:
            name = author.strip()
            if name:
                unique_authors.add(name)

    # opcional: retornar em ordem alfabética
    return sorted(unique_authors)

def index_authors(name):
    qr = f"SELECT id_au, au_name FROM brapci_elastic.ri_authors WHERE au_name = '{name}'"
    row = database.query(qr)
    if not row:
        # Se o autor não existe, insere uma nova entrada
        qi = f"INSERT INTO brapci_elastic.ri_authors (au_name) VALUES ('{name}')"
        database.insert(qi)
        row = database.query(qr)
    return row[0][0]

import sys

def check_create_tables(schema: str = "brapci_elastic"):
    """
    Verifica se cada tabela existe no schema e, se não existir, cria-a usando o DDL correspondente.
    Parâmetros:
      - database: objeto de acesso ao BD, com métodos .query(sql) e .execute(sql)
      - schema: nome do banco/schema onde as tabelas residem
    """

    # Mapeia cada tabela ao seu comando DDL de criação
    table_ddls = {
        "ri_words": """
            CREATE TABLE IF NOT EXISTS `{schema}`.`ri_words` (
                `id_w`    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `w_name`  VARCHAR(30)       NOT NULL,
                `w_lang`  CHAR(3)           NOT NULL DEFAULT 'nn',
                `w_stop`  INT               NOT NULL DEFAULT  0,
                PRIMARY KEY (`id_w`),
                UNIQUE KEY `uq_w_name` (`w_name`)
            ) ENGINE=MyISAM
              DEFAULT CHARSET=utf8mb4
              COLLATE=utf8mb4_unicode_ci;
        """.format(schema=schema),

        "ri_authors": """
            CREATE TABLE IF NOT EXISTS `{schema}`.`ri_authors` (
                `id_au`    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `au_name`  VARCHAR(200)      NOT NULL,
                PRIMARY KEY (`id_au`),
                UNIQUE KEY `uq_au_name` (`au_name`)
            ) ENGINE=MyISAM
              DEFAULT CHARSET=utf8mb4
              COLLATE=utf8mb4_unicode_ci;
        """.format(schema=schema),

        "ri_authors_docs": """
            CREATE TABLE IF NOT EXISTS `{schema}`.`ri_authors_docs` (
                `id_ad`     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `ad_author` BIGINT UNSIGNED NOT NULL,
                `ad_doc`    BIGINT UNSIGNED NOT NULL,
                PRIMARY KEY (`id_ad`),
                UNIQUE KEY `uq_author_doc` (`ad_author`, `ad_doc`)
            ) ENGINE=MyISAM
              DEFAULT CHARSET=utf8mb4
              COLLATE=utf8mb4_unicode_ci;
        """.format(schema=schema),
    }

    # Para cada tabela, verifica existência e cria se necessário
    for table_name, ddl in table_ddls.items():
        check_sql = f"""
            SELECT 1
              FROM information_schema.tables
             WHERE table_schema = '{schema}'
               AND table_name   = '{table_name}'
             LIMIT 1;
        """
        exists = database.query(check_sql)
        if not exists:
            database.insert(ddl)
            print(f"[DONE] Tabela `{table_name}` criada com sucesso.")

    print("[ALL DONE] Verificação e criação de tabelas concluídas.")
    # Se quiser interromper o script após a criação:
    # sys.exit(0)



if __name__ == "__main__":
    print("Processando autores...")
    print("===============================================")
    check_create_tables()

    lista_de_autores = fetch_unique_authors()
    print(f"Total de autores únicos: {len(lista_de_autores)}")

    ix = 0

    for autor in lista_de_autores:
        ix += 1
        if ix % 100 == 0:
            print(f"Processando autor {ix} de {(ix/len(lista_de_autores))*100}%", autor)
        doc = index_authors(autor)
        mod_reverse_index.index(autor,doc)
