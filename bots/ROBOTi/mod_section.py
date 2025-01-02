import mod_literal
import mod_class
import mod_concept
import mod_data
import database
import sys

table = "brapci.section"

def classificationSection():
    qr = f"select * from brapci_oaipmh.oai_setspec where s_section = 1969 and id_s <> 1969"
    row = database.query(qr)

    if row == []:
        print(f"Nenhum section para classificar")
    else:
        for r in row:
            print(f"Classificando {r[3]}")
            classSection(r)
    return 0

def classSection(row):
    Label = row[3]
    ids = 1

    if 'Dossier' in Label:
        ids = 17
    if 'Dossiê' in Label:
        ids = 17

    if 'Anais' in Label:
        ids = 25


    if 'Apresentação' in Label:
        ids = 31
    if 'Expediente' in Label:
        ids = 6
    if 'Avaliadores' in Label:
        ids = 6
    if 'Fórum' in Label:
        ids = 59
    if 'Traduções' in Label:
        ids = 57
    if 'Tradução' in Label:
        ids = 57
    if 'Errata' in Label:
        ids = 58
    if 'Sumário' in Label:
        ids = 55
    if 'SUMARIO' in Label:
        ids = 55
    if 'Prêmio' in Label:
        ids = 52
    if 'Homenagem' in Label:
        ids = 48
    if 'Obituário' in Label:
        ids = 47
    if 'In Memoriam' in Label:
        ids = 47
    if 'Obituário' in Label:
        ids = 47
    if 'Ensaios' in Label:
        ids = 46
    if 'Pecha Kucha' in Label:
        ids = 42
    if 'Pôster' in Label:
        ids = 41
    if 'Poster' in Label:
        ids = 41
    if 'Suplemento' in Label:
        ids = 35
    if 'Memória' in Label:
        ids = 1
    if 'Editorial' in Label:
        ids = 2
    if 'Palestra' in Label:
        ids = 10

    if ids > 0:
        updateSection(row[0], ids)


def updateSection(id_section, id_class):
    qu = f"update brapci_oaipmh.oai_setspec set s_section = {id_class} where id_s = {id_section}"
    database.update(qu)

def getSection(Name):
    qr = f"select sc_rdf from brapci.sections where sc_name = '{Name}'"
    row = database.query(qr)

    if row == []:
        print(f"ERRO DE SECTION {Name}")
    else:
        return row[0][0]
    return 0
