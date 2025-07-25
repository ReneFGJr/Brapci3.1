import xml.etree.ElementTree as ET
import os, sys
import mod_lattes_data_extrator


def extrair_producao_artistica(xml_path):
    ELEMENT = 'PRODUCAO-ARTISTICA-CULTURAL'
    ELEMENT_WORK = 'CURSO-DE-CURTA-DURACAO'
    #**************************
    ELEMENT_DATA = {
        'DADOS-BASICOS-DO-CURSO-DE-CURTA-DURACAO',
        'DETALHAMENTO-DO-CURSO-DE-CURTA-DURACAO', 'AUTORES', 'PALAVRAS-CHAVE',
        'AREAS-DO-CONHECIMENTO', 'SETORES-DE-ATIVIDADE'
    }

    if os.path.exists(xml_path):
        print("O arquivo existe.")
    else:
        print("O arquivo não foi encontrado.")

    tree = ET.parse(xml_path)
    root = tree.getroot()

    producoes = []

    for producao in root.findall('.//' + ELEMENT):
        dados = {}

        for work in producao.findall('.//' + ELEMENT_WORK):
            print("Encontrado WORK:", work.tag)

            for element in ELEMENT_DATA:
                if work.find(element) is not None:
                    print("--Encontrado elemento:", element)
                    mod_lattes_data_extrator.extract_element(work, element, dados)

                else:
                    print("Elemento não encontrado:", element)

    #sys.exit()

    # AUTORES
    autores = []
    for autor in producao.findall('AUTORES'):
        autores.append(autor.attrib)
    dados['autores'] = autores

    # PALAVRAS-CHAVE
    palavras_chave = []
    for palavras in producao.findall('PALAVRAS-CHAVE'):
        palavras_chave.append(palavras.attrib)
    dados['palavras_chave'] = palavras_chave

    # ÁREAS DO CONHECIMENTO
    areas = []
    for area in producao.findall(
            'AREAS-DO-CONHECIMENTO/AREA-DO-CONHECIMENTO'):
        areas.append(area.attrib)
    dados['areas_do_conhecimento'] = areas

    # SETORES DE ATIVIDADE
    setores = []
    for setor in producao.findall('SETORES-DE-ATIVIDADE'):
        setores.append(setor.attrib)
    dados['setores_de_atividade'] = setores

    # INFORMAÇÕES ADICIONAIS
    info_adic = producao.find('INFORMACOES-ADICIONAIS')
    dados['informacoes_adicionais'] = info_adic.attrib if info_adic is not None else {}

    producoes.append(dados)

    return producoes


# Exemplo de uso:
xml_path = 'sources/0024977948247395.xml'  # Caminho do arquivo XML
producoes = extrair_producao_artistica(xml_path)

print("Produções artísticas e culturais ...")
print(producoes)

# Impressão simples dos resultados
for i, p in enumerate(producoes, 1):
    print(f"\n--- Produção {i} ---")
    for k, v in p.items():
        print(f"{k}: {v}")
