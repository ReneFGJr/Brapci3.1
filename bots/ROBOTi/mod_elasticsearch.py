import database
import unicodedata
import json

class ElasticSearchAPI:
    def __init__(self, server='http://143.54.112.91:9200/'):
        self.server = server

    def call(self, endpoint, method, data):
        # Exemplo de chamada HTTP - você pode usar bibliotecas como `requests`
        # Aqui, vamos apenas simular o retorno esperado
        return {'result': 'created', '_version': 1}

def ascii(text):
    # Normaliza o texto para remover acentos e caracteres especiais
    text = unicodedata.normalize('NFKD', text).encode('ascii', 'ignore').decode('ascii')
    # Converte o texto para caixa baixa
    return text.lower()

def nbr_author(name, max_authors=7):
    # Simulando o tratamento de autores
    return name

def upper_case(text):
    # Normaliza o texto para remover acentos e caracteres especiais
    text = unicodedata.normalize('NFKD', text).encode('ascii', 'ignore').decode('ascii')
    # Converte o texto para caixa baixa
    return text.upper()

def export_elasticsearch_v2_2(dta, offset, dtt, limit):
    api = ElasticSearchAPI()
    sx = f'Export ElasticSearch v2.2 - {offset} of {dtt}'

    percent = (offset / dtt * 100) if dtt > 0 else 100
    sx += f' ({percent:.1f}%)<hr>'

    for line in dta:
        dt = {}
        DT = json.loads(line['json'])
        full = ''

        aaut = []
        akey = []
        atit = []
        aabs = []
        asec = []

        # Título
        if 'Title' in DT:
            for ks in DT['Title'].values():
                for term in ks.values():
                    if term.strip():
                        atit.append(ascii(term.lower()))
                        full += f'{ascii(term.lower())} '

        # Seções
        if 'Sections' in DT:
            for ks in DT['Sections'].values():
                for term in ks.values():
                    if term.strip():
                        asec.append(ascii(term.lower().strip()))

        # Autores
        idaa = {}
        if 'Authors' in DT:
            for ks in DT['Authors'].values():
                for idk, term in ks.items():
                    if idk not in idaa and term.strip():
                        term_ascii = ascii(term)
                        full += f'{term_ascii} '
                        aaut.append(nbr_author(upper_case(term_ascii), 7))
                        idaa[idk] = 1

        # Palavras-chave
        if 'Keywords' in DT:
            for ks in DT['Keywords'].values():
                for term in ks:
                    if term.strip():
                        akey.append(ascii(term.lower()))
                        full += f'{ascii(term.lower())} '

        # Resumo
        if 'Abstract' in DT:
            for ks in DT['Abstract'].values():
                for term in ks.values():
                    if term.strip():
                        aabs.append(ascii(term.lower()))
                        full += f'{ascii(term.lower())} '

        # Preenchendo dados
        dt['id'] = line['ID']
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

        dt['year'] = line['YEAR']
        dt['type'] = line['CLASS']
        dt['language'] = DT.get('Idioma', [])
        dt['section'] = asec
        dt['doi'] = DT.get('DOI', '')

        # Enviando dados para o servidor
        id = dt['id']
        print(dt)
        os.exit
        result = api.call(f'brapci3.3/{dt["type"]}/{id}', 'POST', dt)

        # Atualizando o status
        sx += f'{id} => {result["result"]} v.{result["_version"]} ({dt["collection"]})<br>'
        # Simulação de função exported (não implementada)
        # self.exported(id, 0)

    # Verificando se continua a exportação
    if len(dta) == limit:
        sx += f'<meta http-equiv="refresh" content="1;url=/elasticsearch/update_index?offset={offset + limit}">'
    else:
        sx = 'Elastic Search Exported'

    return sx

def dataset_news():
    qr = "select * from brapci_elastic.dataset where new = 1 and use = 0"
    row = database.query(qr)

    for ln in row:
        print(ln)
        # Exemplo de chamada da função

        offset = 0
        dtt = 100
        limit = 10

        resultado = export_elasticsearch_v2_2(ln, offset, dtt, limit)
        print(resultado)
