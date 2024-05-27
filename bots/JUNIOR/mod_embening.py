import re
import pyphen
import database

def separa_silabas(palavra):
    dic = pyphen.Pyphen(lang='pt_BR')
    return dic.inserted(palavra).split('-')

def registrar(texto,lang='pt'):
    texto = re.sub(r'[\r\n\t\?\!\;\.\&\-\/\,\:\(\)\{\}\[\]0-9-]', ' ', texto)
    texto = texto.replace(chr(13),' ')
    texto = texto.replace(chr(10),' ')

    while '  ' in texto:
        texto = texto.replace('  ',' ')

    words = texto.split(' ')
    for w in words:
        registrarWD(w)

def registrarWD(texto,lang='pt'):
    texto = texto.lower()
    texto = re.sub(r'[^a-zA-Z\s]', '', texto)

    wds = []

    sib = separa_silabas(texto)
    print("=======",sib)

    for t in sib:
        # Remove espaços o início e no fim
        t = t.strip()

        #Para se tem espaço nas silabas
        if ' ' in t:
            print("OPS",'[',t,']')
            #quit()
        elif len(t) > 8:
            print("LEN",'[',t,']')
        else:
            if t != '':
                qr = "select id_e from brapci_ia.embending where e_txt = '"+t+"'"
                row = database.query(qr)
                if row == []:
                    qi = "insert into brapci_ia.embending "
                    qi += "(e_txt, e_lang) "
                    qi += " values "
                    qi += f"('{t}','{lang}')"
                    database.insert(qi)

                    row = database.query(qr)

                ################ Palavras
                for rw in row:
                    id = rw[0]
                    wds.append(id)
    print(wds)
