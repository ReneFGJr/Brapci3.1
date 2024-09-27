import database
import unicodedata
import json
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
    lt = ['Editorial','Política editorial','Editorial %','Processo Editorial%',
          'Normas para publicação','Expediente','Expediente %','EDITORIAL, %', 'Normas de Publicação',
          'Apresentação %']
    for q in lt:
        if '%' in q:
            qr = f"update brapci_elastic.dataset set status = 9 where TITLE like '{q}' "
        else:
            qr = f"update brapci_elastic.dataset set status = 9 where TITLE = '{q}' "
        print(qr)
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

        DT = json.loads(line[4])

        full = ''

        aaut = []
        akey = []
        atit = []
        aabs = []
        asec = []

        # Título
        if 'Title' in DT:
            for ks in DT['Title'].values():
                if isinstance(ks, dict):
                    for term in ks.values():
                        if term.strip():
                            atit.append(ascii(term.lower()))
                            full += f'{ascii(term.lower())} '
                elif isinstance(ks, list):
                    for term in ks:
                        if term.strip():
                            atit.append(ascii(term.lower()))
                            full += f'{ascii(term.lower())} '

        # Seções
        if 'Sections' in DT:
            for ks in DT['Sections'].values():
                if isinstance(ks, dict):
                    for term in ks.values():
                        if term.strip():
                            asec.append(ascii(term.lower().strip()))
                elif isinstance(ks, list):
                    for term in ks:
                        if term.strip():
                            asec.append(ascii(term.lower().strip()))

        # Autores
        idaa = {}
        if 'Authors' in DT:
            if isinstance(DT['Authors'], dict):
                for ks in DT['Authors'].values():
                    if isinstance(ks, dict):
                        for idk, term in ks.items():
                            if idk not in idaa and term.strip():
                                term_ascii = ascii(term)
                                full += f'{term_ascii} '
                                aaut.append(nbr_author(upper_case(term_ascii), 7))
                                idaa[idk] = 1
                    elif isinstance(ks, list):
                        for idx, term in enumerate(ks):
                            if idx not in idaa and term.strip():
                                term_ascii = ascii(term)
                                full += f'{term_ascii} '
                                aaut.append(nbr_author(upper_case(term_ascii), 7))
                                idaa[idx] = 1
            elif isinstance(DT['Authors'], list):
                for idx, term in enumerate(DT['Authors']):
                    if idx not in idaa and term.strip():
                        term_ascii = ascii(term)
                        full += f'{term_ascii} '
                        aaut.append(nbr_author(upper_case(term_ascii), 7))
                        idaa[idx] = 1

        # Palavras-chave
        if 'Subject' in DT and isinstance(DT['Subject'], str):
            for ks in DT['Subject'].values():
                if isinstance(ks, dict):
                    for term in ks.values():
                        if term.strip():
                            akey.append(ascii(term.lower()))
                            full += f'{ascii(term.lower())} '
                elif isinstance(ks, list):
                    for term in ks:
                        if term.strip():
                            akey.append(ascii(term.lower()))
                            full += f'{ascii(term.lower())} '

        # Resumo
        if 'Abstract' in DT and isinstance(DT['Abstract'], str):
            for ks in DT['Abstract'].values():
                if isinstance(ks, dict):
                    for term in ks.values():
                        if term.strip():
                            aabs.append(ascii(term.lower()))
                            full += f'{ascii(term.lower())} '
                elif isinstance(ks, list):
                    for term in ks:
                        if term.strip():
                            aabs.append(ascii(term.lower()))
                            full += f'{ascii(term.lower())} '

        # Preenchendo dados
        dt['id'] = line[1]
        dt['full'] = full.strip()
        dt['keyword'] = akey
        dt['abstract'] = aabs
        dt['authors'] = aaut
        dt['title'] = atit

        # Coleta de fonte
        if 'Issue' in DT:
            issue = DT['Issue']
            idj = issue['id_jnl']
            # Simulação de busca no Source (não implementada)
            dt['collection'] = 'ER'
        else:
            dt['collection'] = 'BK' if dt.get('Class') == 'Book' else 'EV'

        ano = line[14]
        try:
            ano = int(ano)
        except ValueError:
            ano = 9999
        dt['year'] = ano
        dt['type'] = line[2]
        dt['language'] = DT.get('Idioma', [])
        dt['section'] = asec
        dt['doi'] = DT.get('DOI', '')

        # Enviando dados para o servidor
        id = dt['id']
        result = api.call(f'brapci3.3/prod/{id}', 'POST', dt)

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


def dataset_news():
    qr = "select * from brapci_elastic.dataset where new = 1 and  `use` = 0 order by id_ds desc"
    row = database.query(qr)

    for ln in row:
        offset = 0
        dtt = 100
        limit = 10
        ID = ln[0]

        update_status(ID,4)
        export_elasticsearch_v2_2(ln, offset, dtt, limit)
        update_status(ID,2)
