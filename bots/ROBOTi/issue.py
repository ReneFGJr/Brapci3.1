import re

def decode(n,lg,vl):
    n = n.lower()

    try:
        vol = vl['vol']
        nr =  vl['nr']
        year =  vl['year']
        theme =  vl['theme']
    except:
        vol = ''
        nr = ''
        year = ''
        theme = ''

    ############################################## Recupera ANO
    ################### method 01 - (YEAR)
    try:
        yearR = re.findall('\(\d{4}\)',n)
        for y in yearR:
            year = y.replace('(','')
            year = year.replace(')','')
    except Exception as e:
        print("Erro ao processar o Ano",e)

    ############################################## Recupera VOLUME
    ################### method 01 - (vol. X)
    try:
        vols = ['vol. ','vol.','v. ','v.']
        vol = ''
        for cg in vols:
            volR = re.findall(cg+'\d',n)
            if (vol == '') and (volR != []):
                vol = volR[0]
                vol = vol.replace(cg,'').strip()
    except Exception as e:
        print("Erro ao processar o VOLUME",e)
    ############################################## Recupera NUMERO
    ################### method 01 - (vol. X)
    try:
        vols = ['nr. ','num.','n. ','n.']
        nr = ''
        for cg in vols:
            volR = re.findall(cg+'\d',n)
            if (nr == '') and (volR != []):
                nr = volR[0]
                nr = vol.replace(cg,'').strip()
    except Exception as e:
        print("Erro ao processar o NUMERO",e)

    ############################################## Finaliza
    try:
        dc = dict(vol=vol,nr=nr,year=year,theme=theme)
    except Exception as e:
        print("Problema ao montar retorno",e)
    #print(dc)
    return dc