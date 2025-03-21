import database
import mod_logs
import mod_nbr
import chardet
import unicodedata
import sys
import re
import mod_class
from charset_normalizer import detect

def check_double_literal(Xclass):
    idClass = mod_class.getClass(Xclass)
    print("Double Title ",Xclass,idClass)

    qr = f"select * from ("
    qr += f" SELECT max(id_d) as id_d, d_r1, n_lang, count(*) as total"
    qr += f" FROM brapci_rdf.rdf_data "
    qr += f" INNER JOIN brapci_rdf.rdf_literal ON d_literal = id_n "
    qr += f" WHERE d_p = {idClass} "
    qr += f" group by n_lang, d_r1"
    qr += f" ) as tabela"
    qr += f" where total > 1"
    qr += f" ORDER BY n_lang, id_d ASC"
    qr += f" limit 1000 "

    row = database.query(qr)
    t = 0
    for line in row:
        idD = line[0]
        ID = line[1]
        qu = f"delete from brapci_rdf.rdf_data where id_d = {idD}"
        database.update(qu)
        #print(qu)
        t = t + 1
        print("= Deleting ",t,Xclass,idD,ID)

def check_end_dot():

    print("156 - Tratamento de assuntos com caracteres especiais")
    prop = 67
    qr = f"select id_n, n_name from brapci_rdf.rdf_data "
    qr += " inner join brapci_rdf.rdf_concept On d_r2 = id_cc"
    qr += " inner join brapci_rdf.rdf_literal on id_n = cc_pref_term"
    qr += f" where d_p = {prop} and n_name <> '' "
    qr += "group by id_n, n_name "
    qr += "order by n_name "

    row = database.query(qr)
    for item in row:
        id_n = item[0]
        title = item[1]
        titleO = item[1]
        title = title.replace('–','-')
        title = title.replace('"','')
        title = title.replace('“','')
        title = title.replace('”','')
        title = title.replace('- ','-')
        title = title.replace(' -','-')
        title = title.replace('<sup>*</sup>','')
        if title[-1] == '.':
            title = title[:-1]
        title = title.strip()

        if (title != titleO):
            qu = f"update brapci_rdf.rdf_literal set n_name = '{title}' where id_n = {id_n}"
            print('=SUBJECT=>',title, id_n)
            database.update(qu)

def detect_encoding(data):
    result = chardet.detect(data)
    return result['encoding']


def correct_utf8_encoding(data, IDn):
    try:
        # Detectar a codificação
        if isinstance(data, str):
            raw_data = data.encode('utf-8',
                                   errors='ignore')  # já pode estar em str
        else:
            raw_data = data

        detected = chardet.detect(raw_data)
        encoding = detected.get('encoding', 'utf-8')
        print(f"Detected encoding: {encoding}")

        # Decodifica usando a codificação detectada
        try:
            decoded = raw_data.decode(encoding, errors='replace')
        except Exception as e:
            print(f"Erro ao decodificar com {encoding}: {e}")
            decoded = raw_data.decode('utf-8', errors='replace')  # fallback

        # Tenta detectar e corrigir dupla codificação
        try:
            decoded = decoded.encode('latin1').decode('utf-8')
        except Exception:
            pass  # ignora se não for o caso

        # Substituição de padrões conhecidos
        correction_map = {
            '[!]': "´",
            '[a~]': "ã",
            '[c,]': "ç",
            '[a3][a3]': "çõ",
            '[a3]': "a",
            '[9c]': "",
            '[aa]': "á",
            '[oh]': "ó",
            '[eh]': "é"
        }

        for key, value in correction_map.items():
            decoded = decoded.replace(key, value)

        # Normaliza caracteres (remove acentos duplicados ou malformados)
        decoded = unicodedata.normalize('NFKC', decoded)

        # Substituições adicionais e limpeza
        decoded = decoded.replace("'", "´").replace('  ', ' ').strip()

        charset = 'utf-8'

        # Atualiza no banco de dados
        if IDn > 0:
            query = f"""
            UPDATE brapci_rdf.rdf_literal
            SET n_name = '{decoded.replace("'", "´")}',
                n_charset = '{charset}'
            WHERE id_n = {IDn}
            """
            database.update(query)  # descomente se a função de atualização estiver disponível

        return decoded if decoded else '[VAZIO]'

    except Exception as e:
        print(f"Erro geral na função correct_utf8_encoding: {e}")
        return data

def check_utf8():
    print("154 - Check UTF8")
    qr = f"SELECT id_n, n_name FROM brapci_rdf.rdf_literal"
    qr += " where n_delete = 0 "
    qr += " and (n_name LIKE '%Ã³%' "
    qr += " or n_name LIKE '%Ã©%' "
    qr += " or n_name LIKE '%ã±%' "
    qr += " or n_name LIKE '%ãº%' "
    qr += " or n_name LIKE '%ã³%' "
    qr += " or n_name LIKE '%ã¡%' "
    qr += " or n_name LIKE '%ã"+chr(128)+"%' "
    qr += " or n_name LIKE '%ã"+chr(129)+"%' "
    qr += " or n_name LIKE '%ã"+chr(130)+"%' "
    qr += " or n_name LIKE '%ã"+chr(157)+"%' "
    qr += " )"
    qr += " AND (n_charset = 'NI')"
    qr += " limit 1000"
    rows = database.query(qr)
    for row in rows:
        original_data = row[1]
        IDn = row[0]
        corrected_data = correct_utf8_encoding(original_data,IDn)

def check_utf8_old():
    qr = "select id_n, n_name "
    qr += " from brapci_rdf.rdf_literal "
    qr += " where n_delete = 0 "
    qr += " and (n_name LIKE '%Ã³%' "
    qr += "  or n_name LIKE '%Ã©%' "
    qr += " or n_name LIKE '%ã±%' "
    qr += " or n_name LIKE '%ãº%' "
    qr += " or n_name LIKE '%ã³%' "
    qr += " or n_name LIKE '%ã¡%' "
    qr += " or n_name LIKE '%ã"+chr(128)+"%' "
    qr += " or n_name LIKE '%ã"+chr(129)+"%' "
    qr += " or n_name LIKE '%ã"+chr(130)+"%' "
    qr += " or n_name LIKE '%ã"+chr(157)+"%' "

    qr += ") "

    row = database.query(qr)
    # Verificar cada registro individualmente


    for id, dados in row:

        if ('Ã' in dados) or ('ã' in dados):

            #dados = unicodedata.normalize('NFKC', dados)
            #dados = dados.replace('Â³','Ó')
            #dados = dados.replace('ã³','ó')
            #dados = dados.replace('ã³','Â')
            #dados = dados.replace('ã£','ó')

            dados = dados.encode('utf-8')
            dados = dados.decode('utf-8')
            dados = dados.encode('latin1')
            dados = dados.decode('utf-8', errors='backslashreplace')
            dados2 = dados
            dados = str(dados)

            dados = dados.replace("\\xc3\\xe3\\x83", "ã")
            dados = dados.replace("\\xc3\\xa7",'ç')
            dados = dados.replace("\\xc3\\x95",'õ')

            dados = dados.replace("\\xc3",'#')

            ok = 0
            if '\\x' in dados:
                dados = dados.replace('\\x93','-')
                dados = dados.replace('\\xa1','!')

                dados = dados.replace('\\xe3\\xb3','ó')
                dados = dados.replace('\\xe3\\x80','.')
                dados = dados.replace('ã\\x82','ã')
                dados = dados.replace('á\\x82','â')

                dados = dados.replace('\\xe3\\x83','ã')

                dados = dados.replace('ã\\x87','ç')
                dados = dados.replace('á\\x87','ç')


                dados = dados.replace('\\xe3\\x87','ç')
                dados = dados.replace('á\\x89','é')
                dados = dados.replace('\\xe3\\x89','é')
                dados = dados.replace('\\xe3\\x8a','ê')
                dados = dados.replace('\\xe3\\x8d','í')

                dados = dados.replace('\\xe3\\x91','ç')
                dados = dados.replace('\\xe3\\x93','ó')

                dados = dados.replace('ã\\x95','õ')
                dados = dados.replace('á\\x95','õ')
                dados = dados.replace("á\x95", "õ")

                dados = dados.replace('\\xe3\\xa0','á')
                dados = dados.replace('\\xe3\\xa1','á')

                dados = dados.replace('\\xe3\\xa7','ç')
                dados = dados.replace('\\xe3\\xa9','é')

                dados = dados.replace('\\xe3\\xaa','ê')
                dados = dados.replace('\\xe3\\xa8','è')
                dados = dados.replace('\\xe3\\xad','í')
                dados = dados.replace('\\xe3\\xaf','ï')

                dados = dados.replace('\\xe3\\xb2','ô')
                dados = dados.replace('\\xe3\\xb5','õ')
                dados = dados.replace('\\xe3\\xbc','u')
                dados = dados.replace('\\xe2\\xbf','¿')

                dados = dados.replace('\\xe3\\xb1','ñ')
                dados = dados.replace('\\xe3\\xba','ú')
                dados = dados.replace('\\xe3\\xba','ú')

                dados = dados.replace('\\xe2\\xa0',' ')


                dados = dados.replace(chr(194),'[\\xxxxxxxxxxxxxx]')
                dados = dados.replace(chr(128),'')
                dados = dados.replace('\\x9c','"')
                dados = dados.replace('\\xb4',' ')
                dados = dados.replace('\\xa6','...')
                dados = dados.replace('\\xc2',' ')
                dados = dados.replace('\\xe2',' ')
                dados = dados.replace('\\xe3','á')
                dados = dados.replace('[espaá\\x91ol]','')
                dados = dados.replace(' \\xb7','')
                dados = dados.replace('á\\x9a','ú')
                dados = dados.replace('á\\xb6','ö')
                dados = dados.replace('\\xe1','á')
                dados = dados.replace('\\xe9','ê')
                dados = dados.replace('\\xea','ê')
                dados = dados.replace('\\xed','e')
                dados = dados.replace('\\xe7','ç')
                dados = dados.replace('\\xf3','ó')
                dados = dados.replace('\\xf4','ô')
                dados = dados.replace('\\xf5','õ')
                dados = dados.replace('\\xfa','ú')
                dados = dados.replace('\\xfe','í')
                dados = dados.replace('\\x99',' ')
                dados = dados.replace('á\\xa2','á')
                dados = dados.replace('á\\xa3','ã')
                dados = dados.replace('â\\x80','')
                dados = dados.replace('á!','á')
                dados = dados.replace('Ã\\xad','í')



                dados = dados.replace('\\xe7á','çã')
                dados = dados.replace('á\\x81','á')
                dados = dados.replace('\\xe0','à')
                dados = dados.replace('i\\xad','í')

                if '\\x' in dados:
                    print("====")
                    print("ORIGINAL",dados2)
                    print("ERROR",dados)
                    dados = dados.encode('utf-8')
                    segment_length = 16
                    for i in range(0, len(dados), segment_length):
                        xdados = dados[i:i + segment_length]
                        print(xdados.hex(' '),xdados)
                        print("============================================================")
                        #sys.exit()

                else:
                    ok = 1
            else:
                ok = 1

            if ok == 1:
                dados = dados.replace('”','')
                dados = dados.replace('“','')
                dados = dados.replace('"','')
                dados = normalize_text(dados)
                qu = f"update brapci_rdf.rdf_literal set n_name = '{dados}' where id_n = {id}"
                database.insert(qu)
                print("=================")
                print("UPDATE: ",qu)

            print("=================================")

def normalize_text(text):
    text = remover_caracteres_especiais(text)
    return ''.join(
        c for c in unicodedata.normalize('NFKD', text)
        if unicodedata.category(c) != 'Mn'
    )

def remover_caracteres_especiais(text):
    # Normaliza e remove diacríticos
    texto_normalizado = unicodedata.normalize('NFKD', text)
    # Remove qualquer caractere não ASCII
    texto_limpo = re.sub(r'[^\x00-\x7F]', '', texto_normalizado)
    return texto_limpo

def check_duplicate():
    qr = "select * from "
    qr += "(select n_name, n_lang, count(*) as total, max(id_n), min(id_n) from brapci_rdf.rdf_literal "
    qr += "where n_delete = 0 group by n_name, n_lang) as tabela "
    qr += "where (total > 1)"

    row = database.query(qr)
    for ln in row:
        ID1 = ln[3]
        ID2 = ln[4]
        qu = f"update brapci_rdf.rdf_concept set cc_pref_term = {ID1} where cc_pref_term = {ID2}"
        database.update(qu)
        qu = f"update brapci_rdf.rdf_data set d_literal = {ID1} where d_literal = {ID2}"
        database.update(qu)
        qu = f"update brapci_rdf.rdf_literal set n_delete = 1 , n_name = concat('[DELETED]',n_name) where id_n = {ID2}"
        database.update(qu)
        print(ln[0])


def check_double_name():
    qr = f"select id_n,n_name, length(n_name)"
    qr += "from brapci_rdf.rdf_literal "
    qr += "where (n_name <> '')"
    #qr += "where (n_name like '%Ana Maria Miel%')"
    row = database.query(qr)
    for ln in row:
        name = ln[1]
        sz = len(name)//2
        name1 = name[:sz]
        name2 = name[sz:]
        sz+=1
        name3 = name[sz:]
        name1 = name1[0:15]
        name2 = name2[0:15]
        name3 = name3[0:15]
        if (name1 == name2) or (name1 == name3):
            if (sz > 10):
                id = ln[0]
                qru = f"update brapci_rdf.rdf_literal set n_name = '{name1}' where id_n = {id}"
                database.update(qru)
                print(name1)
                print(name2)
                print(name3)
                print(sz)
                print("=====================")

def check_title():
    print("153 - Check Title")

    qr = "select * from brapci_vc.words ORDER BY LENGTH(w_word) DESC; "
    rows = database.query(qr)

    prop = 30
    qr = f"select id_n, n_name, n_lang from brapci_rdf.rdf_data "
    qr += " inner join brapci_rdf.rdf_literal on id_n = d_literal"
    qr += " where (n_lang in ('nn','pt','en','es'))"
    qr += f" and d_p = {prop} "
    row = database.query(qr)

    for item in row:
        id_n = item[0]
        title = item[1]
        title = title.replace('"','')
        title = title.replace('“','')
        title = title.replace('”','')
        title = title.strip()
        title = mod_nbr.nbr_title(title,rows)
        lang = item[2]
        if title != item[1]:
            qu = f"update brapci_rdf.rdf_literal set n_name = '{title}', n_lang = '{lang}' where id_n = {id_n}"
            print('==>',title)
            print(" =>",item[1])
            database.update(qu)

def hex_dump(data):
    """
    Exibe um dump hexadecimal de 16 bytes por linha.

    Args:
        data (bytes): Os dados para o dump (em formato de bytes ou string).

    Returns:
        str: Dump hexadecimal formatado.
    """
    if isinstance(data, str):
        data = data.encode('utf-8')  # Converte strings para bytes

    result = []
    for offset in range(0, len(data), 16):
        # Captura 16 bytes a partir do deslocamento
        chunk = data[offset:offset+16]
        # Gera a parte hexadecimal
        hex_part = ' '.join(f"{byte:02x}" for byte in chunk)
        # Gera a parte ASCII (substitui caracteres não imprimíveis por '.')
        ascii_part = ''.join(chr(byte) if 32 <= byte <= 126 else '.' for byte in chunk)
        # Adiciona a linha formatada ao resultado
        result.append(f"{offset:08x}  {hex_part:<48}  {ascii_part}")

    return '\n'.join(result)


def check_trim():
    print("150 - check_trim")
    qr = f"select id_n,n_name from brapci_rdf.rdf_literal "
    qr += " where (n_name like ' %') or (n_name like '%  %') "
    qr += " or (n_name like '% :%')  or (n_name like '%::%')"
    qr += " or (n_name like '% .%') or (n_name like '%<sup%') "
    qr += " or (n_name like '%<br>%') or (n_name like '%</br>%') "
    qr += " or (n_name like '%*%')"
    row = database.query(qr)
    dd=0
    for ln in row:
        name = ln[1]
        nameX = name
        name = name.strip()
        name = name.replace(' :',':')
        name = name.replace(' .','.')
        name = name.replace('::',':')
        name = name.replace('*','')
        name = name.replace('<sup>','')
        name = name.replace('</sup>','')
        name = name.replace('br>','')
        name = name.replace('</br>','')
        name = name.replace('\u00A0', ' ')
        name = name.replace('\u00AF', '-')
        name = name.replace('\u00E2\u0080\u00AF', ' ')
        name = name.replace('\u00E2\u0080\u0094', '-')
        name = name.replace('\u00E2\u0080\u0093', '-')
        name = name.replace('\u0020\u0020',' ')
        if '  ' in name:
            name = name.replace('  ',' ')

        id = ln[0]

        qru = f"update brapci_rdf.rdf_literal set n_name = '{name}' where id_n = {id}"
        database.update(qru)

        dd = dd + 1
        print('=1=>',id,name)
        print('=2=>',id,nameX)
    qd = "COMMIT"
    database.update(qd)
    mod_logs.log('TASK_100',dd)

def check_all():
    qr = f"select id_n,n_name from brapci_rdf.rdf_literal"
    row = database.query(qr)
    for ln in row:
        name = ln[1]
        name2 = name.replace('"','')
        name2 = name2.replace("'",'')
        name2 = name2.replace("&amp.",'&')
        name2 = name2.replace("&amp;",'&')

        save = False
        try:
            n = name2[0]
            if (n > chr(126)):
                name2 = name2.strip()
                if name2 != name:
                    save = True
                    print(name2)

            if (name != name2):
                save = True
                print("1="+name)
                print("2="+name2)
                print("============")
        except:
            print(f"skip erro - {name} - {name2}")
        if save == True:
            #name = name.strip().capitalize()
            id = ln[0]
            qru = f"update brapci_rdf.rdf_literal set n_name = '{name2}' where id_n = {id}"
            database.update(qru)

        #print(name)
    qd = "COMMIT"
    database.update(qd)

def update_term(id,term):
    qr = f"update brapci_rdf.rdf_literal set n_name = '{term}' where id_n = {id}"
    row = database.update(qr)


def register(term,lang,normalize=False):
    if normalize:
        term = normalize_text(term)

    qr = f"select * from brapci_rdf.rdf_literal where (n_name = '{term}') and (n_lang = '{lang}')"
    row = database.query(qr)
    if row == []:
        qri = "insert into brapci_rdf.rdf_literal "
        qri += '(n_name, n_lock, n_lang)'
        qri += ' values '
        qri += f"('{term}',1,'{lang}')"
        row = database.query(qri)
        row = database.query(qr)
    rsp = row[0][0]

    qd = "COMMIT"
    database.update(qd)

    if (rsp == 0):
        print("OPS Register Name")
        quit()
    return rsp
