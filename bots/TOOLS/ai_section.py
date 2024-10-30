import re
import sys_io
import sys
import mod_api

def locate_extrair_sessao(texto):
    Section = ''
    if 'GT- ESPECIAL' in texto:
        Section = 'GT-ESPECIAL'
    if 'GT 8 – Informação e Tecnologia' in texto:
        Section = 'GT-08 (ENANCIB)'
    if 'GT- 10 – Informação e Memória' in texto:
        Section = 'GT-10 (ENANCIB)'
    if 'GT 12 – Informação, Estudos Étnico-Raciais, Gênero e Diversidade' in texto:
        Section = 'GT-12 (ENANCIB)'

    return Section

def locate_extrair_modalidade(texto):
    Modalidade = ''
    if 'Modalidade: Resumo Expandido' in texto:
        Modalidade = 'Resumo Expandido'
    if 'Modalidade: Trabalho Completo' in texto:
        Modalidade = 'Trabalho Completo'
    return Modalidade

    return Section



def extrair_sessao(texto,id):
    # GT
    gt = locate_extrair_sessao(texto)

    if gt != '':
        urlKey = 'https://cip.brapci.inf.br/api/rdf/createConcept/Section?lang=pt&name='
        data = {'apikey': gt}
        rst = mod_api.api_post(urlKey + gt, data)
        IDs = rst['id']

        url = f'https://cip.brapci.inf.br/api/rdf/dataAdd/?source={id}&prop=hasSectionOf&resource={IDs}'
        rst = mod_api.api_post(url, data)

        print("Sessão",gt)
        #hasSectionOf

    # GT
    mod = locate_extrair_modalidade(texto)

    if mod != '':
        urlKey = 'https://cip.brapci.inf.br/api/rdf/createConcept/Section?lang=pt&name='
        data = {'apikey': mod}
        rst = mod_api.api_post(urlKey + mod, data)
        IDs = rst['id']

        url = f'https://cip.brapci.inf.br/api/rdf/dataAdd/?source={id}&prop=hasSectionOf&resource={IDs}'
        rst = mod_api.api_post(url, data)

        url = f'https://cip.brapci.inf.br/api/rdf/dataAdd/?source={id}&prop=hasModalidadSection&resource={IDs}'
        rst = mod_api.api_post(url, data)

        print("Modalidade",mod)
        #hasSectionOf