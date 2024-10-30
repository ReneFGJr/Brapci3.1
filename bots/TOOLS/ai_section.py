import re
import sys_io
import sys

def locate_extrair_sessao(texto):
    Section = ''
    if 'GT- 10 – Informação e Memória' in texto:
        Section = 'GT-10'
    if 'GT- 10 – Informação e Memória' in texto:
        Section = 'GT-10'

    return Section

def extrair_sessao(texto,id):
    # GT
    gt = locate_extrair_sessao(texto)
    print(gt)