import sys_io
import re
import sys
import database
import mod_docling
from pathlib import Path

def extrair_referencias_v2(ID):
    md_filename = Path(mod_docling.fileName(ID))

    if (md_filename.exists()):
        print("Arquivo Markdown encontrado:", md_filename)
        texto = sys_io.readfile(md_filename)
    else:
        print("Arquivo Markdown não encontrado:", md_filename)
        sys.exit()

    ref = ""
    ref_On = False

    # Divide o texto em linhas
    linhas = sys_io.separar_por_linhas(texto)

    # Identificar possível seção de referências
    start_section = locale_referencias_type(texto)

    # Remova linhas repetidas
    linhas = list(dict.fromkeys(linhas))

    # Percorre cada linha a partir de start_section ate o proximo "##"
    listRef = []
    em_referencias = False
    for linha in linhas:
        linha_strip = linha.strip()

        if not em_referencias:
            if linha_strip == start_section.strip():
                em_referencias = True
            continue

        if linha_strip.startswith("##") and linha_strip != start_section.strip():
            break

        if linha_strip:
            listRef.append(linha_strip)

    print("Linhas extraidas:")

    if (len(listRef) > 0):
        saveCited(listRef, ID)

    return listRef

def saveCited(lista,idR):
    qr = f"DELETE from brapci_cited.cited_article where ca_rdf = {idR}"
    database.update(qr)

    jnl_val = "NULL"
    qr = f"select JOURNAL from brapci_elastic.dataset where ID = {idR}"
    row2 = database.query(qr)
    if row2 != [] and row2[0] != []:
        jnl = row2[0][0]
        if isinstance(jnl, (int, float)):
            jnl_val = str(jnl)
        else:
            jnl_txt = str(jnl).replace("'", "´")
            jnl_val = f"'{jnl_txt}'"

    if (len(lista) > 0):
        print("Salvando Citações no banco de dados...")

        for ln in lista:
            ln = re.sub(r'^\s*(?:\d+\s*:\s*)?(?:[-*]\s*)?(?:[1-9]\d{0,2})\.\s+', '', ln)
            ln = ln.replace("'","´")

            qi = "insert into brapci_cited.cited_article "
            qi += "(ca_text,ca_rdf,ca_journal_origem)"
            qi += " values"
            qi += f"('{ln}',{idR},{jnl_val})"
            database.insert(qi)

            print(ln)
    else:
        ln = "Nenhuma citação encontrada."
        print("Nenhuma citação encontrada para salvar.")
        qi = "insert into brapci_cited.cited_article "
        qi += "(ca_text,ca_rdf,ca_journal_origem)"
        qi += " values"
        qi += f"('{ln}',{idR},{jnl_val})"
        database.insert(qi)

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
    tp = ['REFERÊNCIAS', 'Referências', '## Referências', '## REFERÊNCIAS', 'REFERENCIAS', 'Referencias']

    # Divide o texto em linhas
    linhas = sys_io.separar_por_linhas(text)

    ln = 0

    # Percorre cada linha
    for linha in linhas:
        linha_limpa = linha.strip()
        linha_limpa = linha_limpa.replace('1. ',' ')
        linha_limpa = linha_limpa.replace('2. ', ' ')
        linha_limpa = linha_limpa.replace('3. ', ' ')
        linha_limpa = linha_limpa.replace('4. ', ' ')
        linha_limpa = linha_limpa.replace('5. ', ' ')
        linha_limpa = linha_limpa.replace('6. ', ' ')
        linha_limpa = linha_limpa.replace('7. ', ' ')
        linha_limpa = linha_limpa.replace('8. ', ' ')
        linha_limpa = linha_limpa.replace('9. ', ' ')
        linha_limpa = linha_limpa.replace('  ', ' ')

        ln += 1

        for wd in tp:
            # Remover número do capítulo e espaços extras
            # Verifica se a palavra-chave está na linha
            if wd in linha_limpa:
                wd = wd.strip()
                if (wd == linha_limpa):
                    print("Localizado seção de Referências na linha", ln)
                    print("Linha:", linha_limpa)
                    return linha
    return '99999'
