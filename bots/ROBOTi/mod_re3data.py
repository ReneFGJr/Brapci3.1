import requests
import database
import sys, time
import re
import xml.etree.ElementTree as ET
from lxml import etree

def scheme():
    # URL do arquivo XSD
    xsd_url = "http://schema.re3data.org/4-0/re3dataV4-0.xsd"

    # Baixar o arquivo XSD
    response = requests.get(xsd_url)
    if response.status_code == 200:
        xsd_content = response.content
    else:
        raise Exception("Não foi possível baixar o arquivo XSD.")

    # Analisar o XSD com lxml
    xsd_tree = etree.XML(xsd_content)

    # Namespaces (caso necessário)
    namespaces = {'xs': "http://www.w3.org/2001/XMLSchema"}

    # Localizar todos os elementos <xs:element>
    elements = xsd_tree.xpath("//xs:element", namespaces=namespaces)

    # Extrair detalhes de cada elemento
    elements_info = []
    for element in elements:
        element_name = element.attrib.get("name")
        attributes = {key: value for key, value in element.attrib.items()}
        elements_info.append(attributes)

    # Exibir todos os elementos e seus atributos
    print("Elementos novos encontrados no XSD")
    for elem_info in elements_info:
        name = elem_info.get('name', 'N/A');

        qr = f"select * from brapci_repository.rdf_class where c_class = '{name}'";
        row = database.query(qr)
        if len(row) == 0:
            print(f"Nome: {elem_info.get('name', 'N/A')}")
            if is_first_char_upper(name):
                type="C"
            else:
                type="P"
            qr = (f"insert into brapci_repository.rdf_class (c_class, c_prefix, c_type) values ('{name}',21,'{type}');")
            database.insert(qr)
            print(f"NOVO - {name}")
            for attr, value in elem_info.items():
                print(f"  {attr}: {value}")
            print()

def countries():
    # URL do arquivo XSD
    xsd_url = "http://schema.re3data.org/4-0/re3dataV4-0.xsd"

    # Baixar o arquivo XSD
    response = requests.get(xsd_url)
    if response.status_code == 200:
        xsd_content = response.content
    else:
        raise Exception("Não foi possível baixar o arquivo XSD.")

    # Analisar o XSD com lxml
    xsd_tree = etree.XML(xsd_content)

    # Namespaces (caso necessário)
    namespaces = {'xs': "http://www.w3.org/2001/XMLSchema"}

    # Localizar a definição de countries
    countries = xsd_tree.xpath("//xs:simpleType[@name='countries']//xs:enumeration", namespaces=namespaces)

    # Extrair nomes e códigos dos países
    country_list = []
    for country in countries:
        code = country.attrib.get("value")
        name = country.xpath("xs:annotation/xs:documentation", namespaces=namespaces)
        name = name[0].text if name else "N/A"  # Nome do país ou "N/A" se não houver documentação
        country_list.append((name, code))

    # Exibir os países e seus códigos
    print("Novos Países e seus códigos")
    type = 'country'
    for name, code in country_list:
        name = name.replace("'", "´")
        qr = f"select * from brapci_repository.vocabulary where vc_code = '{code}' and vc_type='{type}';"
        row = database.query(qr)
        if len(row) == 0:
            qr = f"insert into brapci_repository.vocabulary (vc_code, vc_name_en, vc_type) values ('{code}', '{name}','{type}');"
            database.insert(qr)
            print(f"NOVO - {code}: {name}")

def content_type_names():
    # URL do arquivo XSD
    xsd_url = "http://schema.re3data.org/4-0/re3dataV4-0.xsd"

    # Baixar o arquivo XSD
    response = requests.get(xsd_url)
    if response.status_code == 200:
        xsd_content = response.content
    else:
        raise Exception("Não foi possível baixar o arquivo XSD.")

    # Analisar o XSD com lxml
    xsd_tree = etree.XML(xsd_content)

    # Namespaces (caso necessário)
    namespaces = {'xs': "http://www.w3.org/2001/XMLSchema"}

    # Localizar a definição de contentTypeNames
    content_types = xsd_tree.xpath("//xs:simpleType[@name='contentTypeNames']//xs:enumeration", namespaces=namespaces)

    # Extrair os tipos de conteúdo
    content_type_list = []
    nr = 0
    for content in content_types:
        code = content.attrib.get("value")  # Código do tipo de conteúdo
        name = content.xpath("xs:annotation/xs:documentation", namespaces=namespaces)
        name = name[0].text.replace("'", "´") if name else "N/A"  # Nome do tipo com substituição de apóstrofe
        if name == "N/A":
            name = code
            nr += 1
            code = f"{nr:03}"
        content_type_list.append((name, code))

    # Exibir os tipos de conteúdo e seus códigos
    print("Novos Tipos de Conteúdo e seus códigos")
    type = 'content_type'
    for name, code in content_type_list:
        name = name.replace("'", "´")
        qr = f"select * from brapci_repository.vocabulary where vc_code = '{code}' and vc_type='{type}';"
        row = database.query(qr)
        if len(row) == 0:
            qr = f"insert into brapci_repository.vocabulary (vc_code, vc_name_en, vc_type) values ('{code}', '{name}','{type}');"
            database.insert(qr)
            print(f"NOVO - {code}: {name}")

def languages():
    # URL do arquivo XSD
    xsd_url = "http://schema.re3data.org/4-0/re3dataV4-0.xsd"

    # Baixar o arquivo XSD
    response = requests.get(xsd_url)
    if response.status_code == 200:
        xsd_content = response.content
    else:
        raise Exception("Não foi possível baixar o arquivo XSD.")

    # Analisar o XSD com lxml
    xsd_tree = etree.XML(xsd_content)

    # Namespaces (caso necessário)
    namespaces = {'xs': "http://www.w3.org/2001/XMLSchema"}

    # Localizar a definição de languages
    languages = xsd_tree.xpath("//xs:simpleType[@name='languages']//xs:enumeration", namespaces=namespaces)

    # Extrair os idiomas
    language_list = []
    for lang in languages:
        code = lang.attrib.get("value")  # Código do idioma
        name = lang.xpath("xs:annotation/xs:documentation", namespaces=namespaces)
        name = name[0].text.replace("'", "´") if name else "N/A"  # Nome do idioma com substituição de apóstrofe
        language_list.append((name, code))

    # Exibir os idiomas e seus códigos
    print("Novos Idiomas e seus códigos")
    type = 'language'
    for name, code in language_list:
        name = name.replace("'", "´")
        qr = f"select * from brapci_repository.vocabulary where vc_code = '{code}' and vc_type='{type}';"
        row = database.query(qr)
        if len(row) == 0:
            qr = f"insert into brapci_repository.vocabulary (vc_code, vc_name_en, vc_type) values ('{code}', '{name}','{type}');"
            database.insert(qr)
            print(f"NOVO - {code}: {name}")

def is_first_char_upper(s):
    # Verifica se a string não está vazia e se o primeiro caractere é maiúsculo
    return bool(s) and s[0].isupper()

def knowledgeAreas():
    # URL do arquivo XSD
    xsd_url = "http://schema.re3data.org/4-0/re3dataV4-0.xsd"

    # Baixar o arquivo XSD
    response = requests.get(xsd_url)
    if response.status_code == 200:
        xsd_content = response.content
    else:
        raise Exception("Não foi possível baixar o arquivo XSD.")

    # Analisar o XSD com lxml
    xsd_tree = etree.XML(xsd_content)

    # Namespaces (caso necessário)
    namespaces = {'xs': "http://www.w3.org/2001/XMLSchema"}

    # Buscar subjectNames (áreas de conhecimento)
    subject_names = xsd_tree.xpath("//xs:simpleType[@name='subjectNames']//xs:enumeration", namespaces=namespaces)
    subject_ids = xsd_tree.xpath("//xs:simpleType[@name='subjectIds']//xs:enumeration", namespaces=namespaces)

    # Verificar se a quantidade de áreas e códigos correspondem
    if len(subject_names) != len(subject_ids):
        raise ValueError("O número de subjectNames e subjectIds não corresponde.")

    # Extrair e associar áreas e códigos
    areas_e_codigos = []
    for name, subject_id in zip(subject_names, subject_ids):
        area = name.attrib.get("value")
        codigo = subject_id.attrib.get("value")
        areas_e_codigos.append((area, codigo))

    # Exibir as áreas de conhecimento e códigos
    print("Áreas de Conhecimento e seus Códigos")
    type = 'knowledge_area'
    for name, code in areas_e_codigos:
        name = name.replace("'", "´")
        qr = f"select * from brapci_repository.vocabulary where vc_code = '{code}' and vc_type='{type}';"
        row = database.query(qr)
        if len(row) == 0:
            qr = f"insert into brapci_repository.vocabulary (vc_code, vc_name_en, vc_type) values ('{code}', '{name}','{type}');"
            database.insert(qr)
            print(f"NOVO - {code}: {name}")

def repository():
    url = 'https://www.re3data.org/api/v1/repositories'
    response = requests.get(url)
    if response.status_code == 200:
        root = ET.fromstring(response.content)
        informacoes = []

        for repository in root.findall('repository'):
            name = repository.find('name').text
            doi = repository.find('doi').text if repository.find('doi') is not None else ''
            api_link = repository.find('link').attrib['href'] if repository.find('link') is not None else ''
            informacoes.append({'name': name, 'doi': doi, 'api_link': api_link})
        return informacoes

def extrair_id_re3data(url):
    match = re.search(r'repository/(\w+)', url)
    if match:
        return match.group(1)
    return None

def country(url):
    response = requests.get('https://www.re3data.org'+url)
    if response.status_code == 200:
        root = ET.fromstring(response.content)
        namespace = {'r3d': 'http://www.re3data.org/schema/2-2'}
        institution_country = root.find('.//r3d:institutionCountry', namespace)
        if institution_country is not None:
            return institution_country.text
        else:
            return 'País não encontrado no XML.'
    else:
        return f'Erro ao acessar a API: {response.status_code}'

# Exemplo de uso
repositorios_brasileiros = repository()

for data in repositorios_brasileiros:
    name = data['name']
    name = name.replace("'", "´")
    api_link = data['api_link']
    doi = data['doi']
    idRe3data = extrair_id_re3data(api_link)
    qr = f"select * from brapci_repository.repository where rep_re3dataID = '{idRe3data}' ;"
    row = database.query(qr)
    if (len(row) == 0):
        countryName = country(api_link)
        print('NOVO - ',countryName,idRe3data,name)

        qr = f"insert into brapci_repository.repository (rep_name, rep_doi, rep_re3dataID, rep_country, rep_rdf) values ('{name}', '{doi}', '{idRe3data}','{countryName}',0);"
        database.insert(qr)
        time.sleep(5)

    else:
        print('EXISTE - ',name)

sys.exit(0)
scheme()
knowledgeAreas()
countries()
content_type_names()
languages()