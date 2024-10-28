import sys_io
import re
import sys

def extrair_referencias(texto):
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
                ref_On = True
        else:
            ref += linha + '\n'


    tipo = identificar_estilo_citacao(texto)
    print("========TIPO=",tipo)
    if (tipo == 'ABNT'):
        ref = preparar_referencias(ref)

    return ref.strip()

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




    #texto = texto.replace(',\n',',')
    #texto = texto.replace(':\n',':')

    #texto = texto.replace(' \n',' ')
    #texto = texto.replace('  ',' ')

    # Letras
    #ltrs = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','(',')',',',';',':','0','1','2','3','4','5','6','7','8','9','.']
    #for l in ltrs:
    #    texto = texto.replace('\n'+l,l)

    # Ano isolado
    #texto = texto.replace('\n20','20')
    #texto = texto.replace('\n19','19')

    #texto = texto.replace('\nAcesso',' Acesso')
    #texto = texto.replace('\nDispon',' Dispon')

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
        strFIM1 = ln[:-2]
        strFIM2 = ln[:-2]
        print("===>",strFIM2)
        if (strINI != strINF):
            linhas[idIn] += ' ' + ln.strip()
            linhas[i] = ''
        else:
            if (strFIM1 == strFIM2):
                linhas[idIn] += ' ' + ln.strip()
                linhas[i] = ''
            else:
                idIn = i

    for i, ln in enumerate(linhas):
        print(i,'=',ln)
    sys.exit()

    # Limpa e junta referências de volta, adicionando quebras de linha
    resultado = []
    referencia_temp = ""
    for parte in referencias:
        # Verifica se a parte parece ser o fim de uma referência
        if parte.isupper() and referencia_temp:
            resultado.append(referencia_temp.strip() + '.')
            referencia_temp = parte
        else:
            referencia_temp += '. ' + parte if referencia_temp else parte

    # Adiciona a última referência
    if referencia_temp:
        resultado.append(referencia_temp.strip() + '.')

    return "\n".join(resultado)

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
