import xml.etree.ElementTree as ET
import os, sys
import mod_lattes_data_extrator


def getElements(element):

    if (element == 'CURSO-DE-CURTA-DURACAO'):
        # Define os elementos que serão extraídos para o curso de curta duração
        ELEMENT_DATA = {
            'DADOS-BASICOS-DO-CURSO-DE-CURTA-DURACAO',
            'DETALHAMENTO-DO-CURSO-DE-CURTA-DURACAO', 'AUTORES', 'PALAVRAS-CHAVE',
            'AREAS-DO-CONHECIMENTO', 'SETORES-DE-ATIVIDADE'
        }
    elif (element == 'PRODUCAO-ARTISTICA-CULTURAL'):
        # Define os elementos que serão extraídos para a produção artística e cultural
        ELEMENT_DATA = {
            'DADOS-BASICOS-DA-PRODUCAO-ARTISTICA-CULTURAL',
            'DETALHAMENTO-DA-PRODUCAO-ARTISTICA-CULTURAL', 'AUTORES', 'PALAVRAS-CHAVE',
            'AREAS-DO-CONHECIMENTO', 'SETORES-DE-ATIVIDADE'
        }
    else:
        {
        print("Elemento desconhecido:", element)
        sys.exit()
        }
    return ELEMENT_DATA

def extrair_producao_artistica(xml_path):


    #**************************
    ELEMENT = 'PRODUCAO-ARTISTICA-CULTURAL'
    ELEMENT_WORK = 'CURSO-DE-CURTA-DURACAO'
    ELEMENT_DATA = getElements(ELEMENT_WORK)

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

            for element in ELEMENT_DATA:
                if work.find(element) is not None:
                    dados = mod_lattes_data_extrator.extract_element(work, element, dados)
                else:
                    print("Elemento não encontrado:", element)

    producoes.append(dados)
    return producoes
