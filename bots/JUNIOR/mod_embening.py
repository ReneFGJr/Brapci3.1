import re
import pyphen
import database

def separa_silabas(palavra):
    dic = pyphen.Pyphen(lang='pt_BR')
    return dic.inserted(palavra).split('-')

def registrar(texto,lang='pt'):
    texto = re.sub(r'[\r\n\t\?\!\;\.\&\-\/\,\:\(\)\{\}\[\]0-9-]', ' ', texto)
    print(texto)

def registrarWD(texto,lang='pt'):
    texto = texto.lower()
    texto = re.sub(r'[^a-zA-Z\s]', '', texto)

    sib = separa_silabas(texto)

    for t in sib:
        # Remove espaços o início e no fim
        t = t.strip()

        #Para se tem espaço nas silabas
        if ' ' in t:
            print("OPS",'[',t,']')
            #quit()
        elif len(t) > 5:
            print("LEN",'[',t,']')
        else:
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
