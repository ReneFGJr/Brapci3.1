import re
import pyphen
import database

def separa_silabas(palavra):
    dic = pyphen.Pyphen(lang='pt_BR')
    return dic.inserted(palavra).split('-')

def registrar(texto,lang='pt'):
    texto = texto.lower()
    texto = re.sub(r'[^a-zA-Z\s]', '', texto)

    sib = separa_silabas(texto)

    for t in sib:
        if ' ' in t:
            print("OPS",t)
            quit()
        if t != '':
            qr = "select * from brapci_ia.embending where e_txt = '"+t+"'"
            row = database.query(qr)
            if row == []:
                qi = "insert into brapci_ia.embending "
                qi += "(e_txt, e_lang) "
                qi += " values "
                qi += f"('{t}','{lang}')"
                database.insert(qi)
                print("Inserido",t,lang)
            else:
                print("skip",t)
