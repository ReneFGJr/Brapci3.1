import re
import sys_io
import sys
import mod_api

# Dicionário para mapeamento das sessões
GT_SECTIONS = {
    'GTESPECIAL': 'GT-ESPECIAL',
    'GTEspecial': 'GT-ESPECIAL',
    'GT1 Estudos': 'GT-01 (ENANCIB)',
    'GT2 Organização': 'GT-02 (ENANCIB)',
    'GT3 Mediação': 'GT-03 (ENANCIB)',
    'GT4 Gestão': 'GT-04 (ENANCIB)',
    'GT5 Política': 'GT-05 (ENANCIB)',
    'GT6 Informação': 'GT-06 (ENANCIB)',
    'GT7 Produção': 'GT-07 (ENANCIB)',
    'GT8 Informação': 'GT-08 (ENANCIB)',
    'GT9 Museu': 'GT-09 (ENANCIB)',
    'GT10 Informação': 'GT-10 (ENANCIB)',
    'GT11 Informação': 'GT-11 (ENANCIB)',
    'GT12 Informação': 'GT-12 (ENANCIB)'
}

GT_MODALIDADE = {
    'Modalidade: Resumo Expandido':'Resumo Expandido',
    'Modalidade: Trabalho Completo':'Trabalho Completo',
    'Modalidade: Texto completo':'Trabalho Completo',
    'Modalidade: Trabalho completo':'Trabalho Completo',
    'Artigo completo':'Trabalho Completo',
    'Modalidade da Apresentação: Pôster':'Pôster',
}

# Função para localizar e extrair a sessão
def locate_extrair_sessao(texto):
    texto = texto[:1200]
    texto = texto.replace('GT-', 'GT').replace('- ',' ').replace(' –', ' ').replace('–', '').replace('  ',' ').replace('GT ', 'GT').replace('GT0', 'GT').replace('&', 'e')
    sectionX = ''
    maxPos = 99999999
    for key, section in GT_SECTIONS.items():
        if key in texto:
            posicao = texto.find(key)
            if posicao < maxPos:
                maxPos = posicao
                sectionX = section
        if key.upper() in texto:
            if posicao < maxPos:
                maxPos = posicao
                sectionX = section
    return sectionX

# Função para localizar e extrair a modalidade
def locate_extrair_modalidade(texto):
    texto = texto[:1200]
    texto = texto.replace('GT-', 'GT').replace('- ',' ').replace(' –', ' ').replace('–', '').replace('  ',' ').replace('GT ', 'GT').replace('GT0', 'GT').replace('&', 'e')
    sectionX = ''
    maxPos = 99999999
    for key, section in GT_MODALIDADE.items():
        if key in texto:
            posicao = texto.find(key)
            if posicao < maxPos:
                maxPos = posicao
                sectionX = section
        if key.upper() in texto:
            if posicao < maxPos:
                maxPos = posicao
                sectionX = section
    return sectionX

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
    else:
        print("ERRO Modalidade",mod)
