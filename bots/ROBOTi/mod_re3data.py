import requests
from lxml import etree

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
print("Áreas de Conhecimento e seus Códigos:")
for area, codigo in areas_e_codigos:
    print(f"insert into brapci_repository.subjects (sb_code, sb_name_en) values ('{codigo}','{area}');")
