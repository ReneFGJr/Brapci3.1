import re
import sys_io
import sys
import mod_api

def locate_extrair_sessao(texto):
    Section = ''
    if 'GT- 10 – Informação e Memória' in texto:
        Section = 'GT-10 (ENANCIB)'
    if 'GT- 10 – Informação e Memória' in texto:
        Section = 'GT-10 (ENANCIB)'

    return Section

def extrair_sessao(texto,id):
    # GT
    gt = locate_extrair_sessao(texto)

    if gt != '':
        urlKey = 'https://cip.brapci.inf.br/api/rdf/createConcept/Section?lang=pt&name='
        data = {'apikey': gt}
        rst = mod_api.api_post(urlKey + gt, data)
        print(rst)
        #hasSectionOf