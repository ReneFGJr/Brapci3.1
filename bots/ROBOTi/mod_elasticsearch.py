import database
import unicodedata
import json,sys
import requests

class ElasticSearchAPI:
    def __init__(self, server='http://143.54.112.91:9200/'):
        self.server = server

    def call(self, endpoint, method, data):
        url = self.server + endpoint

        headers = {
            'Content-Type': 'application/json'
        }

        try:
            if method == 'POST':
                response = requests.post(url, headers=headers, data=json.dumps(data))
            elif method == 'PUT':
                response = requests.put(url, headers=headers, data=json.dumps(data))
            elif method == 'GET':
                response = requests.get(url, headers=headers)
            elif method == 'DELETE':
                response = requests.delete(url, headers=headers)

            # Verifica se a resposta é válida (status code 2xx)
            if response.status_code in range(200, 300):
                return response.json()
            else:
                # Se houver erro, retorna o código e a mensagem de erro
                return {'error': response.status_code, 'message': response.text}

        except Exception as e:
            return {'error': 'Exception', 'message': str(e)}

def ascii(text):
    # Normaliza o texto para remover acentos e caracteres especiais
    text = unicodedata.normalize('NFKD', text).encode('ascii', 'ignore').decode('ascii')
    # Converte o texto para caixa baixa
    return text.lower()

def remove_editorial():
    print("183 - Removendo editoriais")
    lt = ['Editorial','Política editorial','Editorial %','Processo Editorial%',
          'Normas para publicação','Expediente','Expediente %','EDITORIAL, %', 'Normas de Publicação',
          'Apresentação %']
    for q in lt:
        if '%' in q:
            qr = f"update brapci_elastic.dataset set status = 2 where TITLE like '{q}' "
        else:
            qr = f"update brapci_elastic.dataset set status = 2 where TITLE = '{q}' "
        database.update(qr)

def nbr_author(name, max_authors=7):
    # Simulando o tratamento de autores
    return name

def upper_case(text):
    # Normaliza o texto para remover acentos e caracteres especiais
    text = unicodedata.normalize('NFKD', text).encode('ascii', 'ignore').decode('ascii')
    # Converte o texto para caixa baixa
    return text.upper()

def export_elasticsearch_v2_2(line, offset, dtt, limit):
    api = ElasticSearchAPI()
    sx = f'Export ElasticSearch v2.2 - {offset} of {dtt}'

    percent = (offset / dtt * 100) if dtt > 0 else 100
    sx += f' ({percent:.1f}%)<hr>'

    if line:
        dt = {}
        dt['id'] = line[1]
        dt['class'] = line[3]
        dt['collection'] = line[4]
        dt['year'] = line[16]

        json_str = line[5]  # ← use um nome diferente
        try:
            JS = json.loads(json_str)
        except json.JSONDecodeError as e:
            print(f"[ERRO] JSON malformado: {json_str}")
            print(e)
            return

        data = JS

        # Extrair keywords em português
        all_keywords = []

        for lang_terms in data.get("Subject", {}).values():
            all_keywords.extend(lang_terms)

        all_abstract = []

        authors_info = data.get("authors", [])
        author_names = [a["name"] for a in authors_info if "name" in a]

        dt = {}
        dt['id'] = line[1]
        dt['class'] = line[3]
        dt['collection'] = line[4]
        dt['year'] = line[16]
        dt['authors'] = author_names
        dt['keywords'] = all_keywords
        dt['abstract'] = all_abstract
        dt['journal'] = line[6]
        dt['langage'] = []
        print(dt)
        sys.exit()
        result = api.call(f'brapci3.4/prod/{id}', 'POST', dt)

        # Atualizando o status
        try:
            sx = f'{id} => {result["result"]} v.{result["_version"]} ({dt["collection"]})'
            print(sx)
        except Exception as e:
            print("+=============================== ERRO")
            print(result)
            return {'error': 'Exception', 'message': str(e)}
        # Simulação de função exported (não implementada)
        # self.exported(id, 0)

    # Verificando se continua a exportação
    if len(line) == limit:
        sx += f'LIMITE'
    else:
        sx = 'Elastic Search Exported'

    return sx

def update_status(ID,status):
    qu = f"update brapci_elastic.dataset set new = {status} where id_ds = {ID}"
    database.update(qu)

def reindex():
    qu = "update brapci_elastic.dataset set new = 1 where ((new = 4) or (new = 2) or (new = 0)) and `use` = 0"
    database.update(qu)

def remove(id):
    qu = f"delete from brapci_elastic.dataset where (id_ds = {id})"
    database.update(qu)

def dataset_news():
    qr = "select * from brapci_elastic.dataset where new = 1 and  `use` = 0 order by id_ds desc"
    row = database.query(qr)
    if not row:
        print("No new dataset found.")
        return
    for ln in row:
        offset = 0
        dtt = 100
        limit = 10
        ID = ln[0]

        update_status(ID,4)
        export_elasticsearch_v2_2(ln, offset, dtt, limit)
        update_status(ID,2)
