import database
import unicodedata
import json,sys
import requests
import unicodedata
import re

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

def ascii(texto):
    if texto is None:
        return ""
    # 1) remover acentos
    nfkd = unicodedata.normalize("NFKD", str(texto))
    sem_acentos = "".join(ch for ch in nfkd if not unicodedata.combining(ch))
    # 2) caixa baixa
    sem_acentos = sem_acentos.lower()
    # 3) manter só letras a-z, dígitos e espaços
    apenas_basico = re.sub(r"[^a-z0-9\s]", " ", sem_acentos)
    # 4) colapsar espaços
    return re.sub(r"\s+", " ", apenas_basico).strip()

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

import json
import sys

def export_elasticsearch_v2_3(row, offset, dtt, limit):
    """
    Envia TODOS os registros em uma ÚNICA chamada ao Elasticsearch via _bulk (NDJSON).
    Mantém o índice/tipo do caminho anterior: brapci3.4/prod/{id}
    """
    api = ElasticSearchAPI()
    sx = f'Export ElasticSearch v2.3 - {offset} of {dtt}'
    percent = (offset / dtt * 100) if dtt > 0 else 100
    sx += f' ({percent:.1f}%)<hr>'

    # Monta o NDJSON (2 linhas por doc: meta + fonte)
    ndjson_lines = []
    ids = []

    for line in row:
        try:
            json_str = line[6]
            data = json.loads(json_str)
        except json.JSONDecodeError as e:
            print(f"[ERRO] JSON malformado para ID {line[0]}: {e}")
            continue

        # keywords (todas as línguas presentes em Subject)
        all_keywords = []
        for lang_terms in (data.get("Subject") or {}).values():
            all_keywords.extend(lang_terms or [])

        all_abstract = (data.get("Abstract") or {}).get('pt')
        section = line[14]
        authors_info = data.get("authors", []) or []
        author_names = [a.get("name") for a in authors_info if isinstance(a, dict) and "name" in a]

        # documento a ser indexado
        doc = {
            'id': line[0],
            'class': line[4],
            'title': ascii(line[10]),
            'collection': line[5],
            'year': line[17],
            'authors': ascii(author_names),
            'keywords': ascii(all_keywords),
            'abstract': ascii(all_abstract),
            'journal': ascii(line[7]),
            'langage': [],
            'full': [],
            'section': ascii(section),
            'type': line[4],
        }

        _id = line[0]
        ids.append(_id)

        # Linha de METADADOS do bulk
        # OBS: se seu ES não usa mais _type, remova "_type": "prod" aqui.
        meta = {"index": {"_index": "brapci3.4", "_type": "prod", "_id": _id}}
        ndjson_lines.append(json.dumps(meta, ensure_ascii=False))
        ndjson_lines.append(json.dumps(doc, ensure_ascii=False))

    if not ndjson_lines:
        return sx + "<br>Nenhum documento válido para enviar."

    # Payload NDJSON (termina com \n)
    payload = "\n".join(ndjson_lines) + "\n"

    # Envia em uma ÚNICA chamada
    try:
        result = api.call(
            'brapci3.4/prod/_bulk',
            'POST',
            payload,
            headers={'Content-Type': 'application/x-ndjson'}
        )
    except Exception as e:
        print("+=============================== ERRO CHAMADA BULK")
        print(str(e))
        return {'error': 'Exception', 'message': str(e)}

    # Processa resposta do bulk
    erros = result.get("errors", False)
    items = result.get("items", [])

    ok_count = 0
    err_count = 0
    msgs_erro = []

    for it in items:
        # cada item possui a ação (index/create/update/delete)
        action = next(iter(it))
        info = it[action]
        status = info.get("status", 0)
        _id = info.get("_id")
        if 200 <= status < 300:
            ok_count += 1
        else:
            err_count += 1
            reason = (info.get("error") or {}).get("reason", "erro não informado")
            msgs_erro.append(f"{_id} => status {status}: {reason}")

    # logs de saída
    print(f"{ok_count} documentos indexados; {err_count} com erro.")
    if msgs_erro:
        print("Erros (primeiros 10):")
        for m in msgs_erro[:10]:
            print(" -", m)

    # Se quiser marcar como exportados: chame sua função aqui em lote
    # ex: self.exported(ids, 0)

    # Mensagem final
    if len(row) == limit:
        sx += 'LIMITE'
    else:
        sx = 'Elastic Search Exported (bulk)'

    return sx


def export_elasticsearch_v2_2(line, offset, dtt, limit):
    api = ElasticSearchAPI()
    sx = f'Export ElasticSearch v2.2 - {offset} of {dtt}'

    percent = (offset / dtt * 100) if dtt > 0 else 100
    sx += f' ({percent:.1f}%)<hr>'

    if line:
        dt = {}

        json_str = line[6]  # ← use um nome diferente
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

        all_abstract = data.get("Abstract", {}).get('pt')

        section = line[14]

        authors_info = data.get("authors", [])
        author_names = [a["name"] for a in authors_info if "name" in a]

        dt = {}
        dt['id'] = line[0]
        dt['class'] = line[4]
        dt['title'] = ascii(line[10])
        dt['collection'] = line[5]
        dt['year'] = line[17]
        dt['authors'] = ascii(author_names)
        dt['keywords'] = ascii(all_keywords)
        dt['abstract'] = ascii(all_abstract)
        dt['journal'] = ascii(line[7])
        dt['langage'] = []
        dt['full'] = []
        dt['section'] = ascii(section)
        dt['type'] = line[4]
        id = line[0]
        result = api.call(f'brapci3.4/prod/{id}', 'POST', dt)

        # Atualizando o status
        try:
            sx = f'{id} => {result["result"]} v.{result["_version"]} ({dt["collection"]})'
            print(sx)
        except Exception as e:
            print("+=============================== ERRO")
            print(result)
            sys.exit()
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

    offset = 0
    dtt = 100
    limit = 100

    rst = export_elasticsearch_v2_3(row, offset, dtt, limit)
    print(rst)
    sys.exit()

    for ln in row:


        update_status(ID,4)
        export_elasticsearch_v2_2(ln, offset, dtt, limit)
        update_status(ID,2)
