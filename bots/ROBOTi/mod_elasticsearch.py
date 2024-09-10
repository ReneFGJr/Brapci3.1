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

def export_elasticsearch_v2_2(line, offset, dtt, limit):
    api = ElasticSearchAPI()
    sx = f'Export ElasticSearch v2.2 - {offset} of {dtt}'

    percent = (offset / dtt * 100) if dtt > 0 else 100
    sx += f' ({percent:.1f}%)<hr>'

    if line:
        dt = {}

        DT = json.loads(line[4])

        print(DT)

        full = ''

        aaut = []
        akey = []
        atit = []
        aabs = []
        asec = []

        print("===",DT['Title'].values)

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
        print("========================",atit)

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
        if 'Keywords' in DT:
            for ks in DT['Keywords'].values():
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
        if 'Abstract' in DT:
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
        result = api.call(f'brapci3.3/{dt["type"]}/{id}', 'POST', dt)

        # Atualizando o status
        sx += f'{id} => {result["result"]} v.{result["_version"]} ({dt["collection"]})<br>'
        # Simulação de função exported (não implementada)
        # self.exported(id, 0)

    # Verificando se continua a exportação
    if len(line) == limit:
        sx += f'<meta http-equiv="refresh" content="1;url=/elasticsearch/update_index?offset={offset + limit}">'
    else:
        sx = 'Elastic Search Exported'

    return sx

def dataset_news():
    qr = "select * from brapci_elastic.dataset where new = 1 and  `use` = 0"
    row = database.query(qr)

    for ln in row:
        print(ln)
        # Exemplo de chamada da função

        offset = 0
        dtt = 100
        limit = 10

        resultado = export_elasticsearch_v2_2(ln, offset, dtt, limit)
        print(resultado)
