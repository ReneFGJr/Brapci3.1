
import sys
def register(doi,name,conexao):
    name = name.replace("'","´")
    name = name.replace('"',"´")
    name = name.replace('&quot;',"")



    sql = f"SELECT id_dp , dp_prefix from brapci_persistent_indicador.doi_source where dp_prefix = '"+doi+"'"
    #print(sql)
    cursor = conexao.cursor()
    cursor.execute(sql)
    linha = cursor.fetchone()
    results = cursor.fetchall()
    cursor.close()

    if (results == []):
        sql = f"INSERT INTO brapci_persistent_indicador.doi_source (dp_prefix,dp_group,dp_name) VALUES ('"+doi+"','"+doi+"','"+name+"')"
        cursos_insert = conexao.cursor()
        cursos_insert.execute(sql)
        conexao.commit()
        cursos_insert.close()
        print("INSERIDO "+doi)
    else:
        print("Já existe "+doi)
