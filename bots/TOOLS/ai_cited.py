import sys_io
import re
import sys
import database

def extrair_referencias(texto,idR):
    start_section = locale_referencias_type(texto)
    texto = sys_io.remove_legendas(texto)

    ref = ""
    ref_On = False

    # Divide o texto em linhas
    linhas = sys_io.separar_por_linhas(texto)

    # Percorre cada linha
    for linha in linhas:
        if not ref_On:
            if start_section in linha:
                print(linha)
                #ref_On = True
        else:
            ref += linha + '\n'


    tipo = identificar_estilo_citacao(texto)
    print("========TIPO=",tipo)
    if (tipo == 'ABNT'):
        ref = preparar_referencias(ref)
        saveCited(ref,idR)
    return ref

def saveCited(lista,idR):
    qr = f"select * from brapci_cited.cited_article where ca_rdf = {idR}"
    row = database.query(qr)
    if row == []:
        ##################### Recupera Dados
        qr = f"select JOURNAL, ID from brapci_elastic.dataset where ID = {idR}"
        row2 = database.query(qr)
        if not (row2 == []):
            jnl = row2[0][0]
            for ln in lista:
                ln = ln.replace("'","´")
                qi = "insert into brapci_cited.cited_article "
                qi += "(ca_text,ca_rdf,ca_journal_origem)"
                qi += " values"
                qi += f"('{ln}',{idR},{jnl})"
                database.insert(qi)
    else:
        print("Já existe")

def identificar_estilo_citacao(referencia):
    referencia = referencia.strip()

    # Padrões para identificar cada estilo
    padroes = {
        'ABNT': r'^[A-Z]+, [A-Z]\.|\([A-Z]\.\)|\bvol\.|ed\.|p\.\s\d+',
        'Vancouver': r'^\d+|\b(?:[A-Z][a-z]+ )?[A-Z]\b',
        'APA': r'^[A-Z][a-z]+, [A-Z]\. \([0-9]{4}\)|[A-Z]\. \([0-9]{4}\),'
    }

    # Detecta o estilo de referência com base nos padrões
    for estilo, padrao in padroes.items():
        if re.search(padrao, referencia):
            return estilo

    return "Desconhecido"

def preparar_referencias(texto):

    texto = texto.replace(', ',',')
    texto = texto.replace(': ',':')
    texto = texto.replace('; ',';')
    texto = texto.replace('º ','º')
    texto = texto.replace('ª ','ª')

    # Letras
    ltrs = ['0','1','2','3','4','5','6','7','8','9','(',')','[',']',',',';','-','.']
    for l in ltrs:
        texto = texto.replace('\n'+l,l)
    ltrs = ['(',')','[',']',',',';','-','/','\\',':','º','ª']
    for l in ltrs:
        texto = texto.replace(l+'\n',l)

    texto = texto.replace(',',', ')
    texto = texto.replace(':',': ')
    texto = texto.replace(';','; ')
    texto = texto.replace('http: ','http:')
    texto = texto.replace('https: ','https:')
    texto = texto.replace('º','º ')
    texto = texto.replace('ª','ª ')


    linhas = sys_io.separar_por_linhas(texto)
    #for i,ln in linhas:
    idIn = 0
    for i, ln in enumerate(linhas):
        strINI = ln[:2].upper()
        strINF = ln[:2]
        ln2 = sys_io.remover_numeros(ln.strip())
        ln2 = ln2.replace('-','')
        ln2 = ln2.replace('/','')
        ln2 = ln2.strip()

        strFIM1 = ln2[-4:].upper()
        strFIM2 = ln2[-4:]

        if (strINI != strINF):
            linhas[idIn] += ' ' + ln.strip()
            linhas[i] = ''
        else:
            idIn = i

    lns = []
    for i, ln in enumerate(linhas):
        ln = ln.strip()
        if (ln != ''):
            lns.append(ln)
    # Pente Fino 1

    for i, ln in enumerate(lns):
        if not sys_io.soNumero(ln):
            try:
                if (ln != ''):
                    lns[i] = lns[i] + ' ' + lns[i+1]
                    lns[i+1] = ''
            except:
                print(i,"OPS",ln)

    # Gerar arquivo de referências
    ref = []
    for i, ln in enumerate(lns):
        if (ln != ''):
            ln = ln.replace('  ',' ')
            ref.append(ln)
    return ref

def locale_referencias_type(text):
    tp = ['REFERÊNCIAS','Referências']

    # Divide o texto em linhas
    linhas = sys_io.separar_por_linhas(text)

    # Percorre cada linha
    for linha in linhas:
        for wd in tp:
            # Verifica se a palavra-chave está na linha
            if wd in linha:
                return wd.strip()
    return ""
