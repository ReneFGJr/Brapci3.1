import pyphen
import database

def separa_silabas(palavra):
    dic = pyphen.Pyphen(lang='pt_BR')
    return dic.inserted(palavra).split('-')

def registrar(texto):
    texto = texto.lower()
    sib = separa_silabas(texto)

    for t in sib:
        if t != None:
            print(t)
