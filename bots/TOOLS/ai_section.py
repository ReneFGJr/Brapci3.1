import re
import sys_io
import sys
import mod_api

# Dicionário para mapeamento das sessões
GT_SECTIONS = {
    'GTESPECIAL': 'GT-ESPECIAL',
    'GT1 Estudos Históricos e Epistemológicos da Ciência da Informação': 'GT-01 (ENANCIB)',
    'GT2 Organização e Representação do Conhecimento': 'GT-02 (ENANCIB)',
    'GT3 Mediação': 'GT-03 (ENANCIB)',
    'GT4 Gestão da Informação e do Conhecimento': 'GT-04 (ENANCIB)',
#    'GT4 GESTÃO DA INFORMAÇÃO E DO CONHECIMENTO': 'GT-04 (ENANCIB)',
    'GT5 Informação e Economia da Informação': 'GT-05 (ENANCIB)',
    'GT6 Informação, Educação e Trabalho': 'GT-06 (ENANCIB)',
    'GT7 Produção e Comunicação': 'GT-07 (ENANCIB)',
    'GT8 Informação e Tecnologia': 'GT-08 (ENANCIB)',
    'GT9 Museu, Patrimônio e Informação': 'GT-09 (ENANCIB)',
    'GT10 Informação e Memória': 'GT-10 (ENANCIB)',
    'GT11 Informação e Saúde': 'GT-11 (ENANCIB)',
    'GT12 Informação, Estudos Étnico-Raciais, Gênero e Diversidade': 'GT-12 (ENANCIB)',
}

# Função para localizar e extrair a sessão
def locate_extrair_sessao(texto):
    texto = texto.replace('GT-', 'GT').replace(' –', '').replace('GT ', 'GT').replace('&', 'e')
    print(texto[:100])
    for key, section in GT_SECTIONS.items():
        if key in texto:
            return section
        if key.upper() in texto:
            return section
    return ''

# Função para localizar e extrair a modalidade
def locate_extrair_modalidade(texto):
    if 'Modalidade: Resumo Expandido' in texto:
        return 'Resumo Expandido'
    elif 'Modalidade: Trabalho Completo' in texto:
        return 'Trabalho Completo'
    return ''

# Função para fazer a chamada de API com o nome da sessão/modalidade
def post_to_api(name, prop, id):
    urlKey = f'https://cip.brapci.inf.br/api/rdf/createConcept/Section?lang=pt&name={name}'
    data = {'apikey': name}
    response = mod_api.api_post(urlKey, data)
    IDs = response['id']
    url = f'https://cip.brapci.inf.br/api/rdf/dataAdd/?source={id}&prop={prop}&resource={IDs}'
    mod_api.api_post(url, data)

# Função principal para extrair sessão e modalidade
def extrair_sessao(texto, id):
    # Extrair e enviar sessão
    gt = locate_extrair_sessao(texto)
    if gt:
        post_to_api(gt, 'hasSectionOf', id)
        print("Sessão:", gt)

    # Extrair e enviar modalidade
    mod = locate_extrair_modalidade(texto)
    if mod:
        post_to_api(mod, 'hasSectionOf', id)
        post_to_api(mod, 'hasModalidadSection', id)
        print("Modalidade:", mod)
