import mod_literal
import mod_class
import mod_concept
import mod_data
import database
import mod_logs
import re
import unicodedata

def check_use_zero():
    qr = "update "
    qr += "brapci_rdf.rdf_concept "
    qr += "set cc_use = id_cc "
    qr += "where cc_use = 0 "
    database.update(qr)

def check_dupla_remissiva():
    check_use_zero()
    print("201 - Check Dupla Remissiva")
    dd = 0
    qr = "SELECT c1.id_cc, c2.id_cc, c2.cc_use "
    qr += "FROM brapci_rdf.rdf_concept as c1 "
    qr += "INNER JOIN brapci_rdf.rdf_concept as c2 ON c1.cc_use = c2.id_cc "
    qr += "WHERE c1.cc_use <> c1.id_cc "
    qr += "and c2.id_cc <> c2.cc_use "

    row = database.query(qr)

    for reg in row:
        ID2 = reg[0]
        ID1 = reg[2]
        print("CheckD -",ID1,'<=',ID2)
        mod_data.remicive(ID1,ID2)
        dd = dd + 1

    qd = "COMMIT"
    database.update(qd)

    mod_logs.log('TASK_202',dd)

def check_remissiva_old():
    print("202 - Check author remissive")

    dd = 0

    qr = "SELECT id_cc, cc_use, id_n, n_name FROM brapci_rdf.rdf_concept "
    qr += "inner join brapci_rdf.rdf_literal ON cc_pref_term = id_n "
    qr += "inner join brapci_rdf.rdf_data ON ((d_r1 = id_cc) and (d_literal = 0)) "
    qr += "WHERE (cc_use <> id_cc) and (d_r2 > 0) and (cc_use <> 0)"
    print(qr)
    row = database.query(qr)

    ID2A = 0
    ID1A = 0

    for reg in row:
        ID2 = reg[0]
        ID1 = reg[1]
        NAME = reg[3]
        if (ID1 != ID1A) and (ID2A != ID2):
            print("Check1 -",ID1,'<=',ID2,NAME)
            mod_data.remicive(ID1,ID2)
            dd = dd + 1
        ID2A = ID2
        ID1A = ID1

    qd = "COMMIT"
    database.update(qd)

    mod_logs.log('TASK_202',dd)
    return ""

def check_remissivaDB():
    print("205 - Check author remissive in database")

    dd = 0

    IDClass = mod_class.getClass("Person")

    qu = "TRUNCATE brapci_zz.concepts"
    database.update(qu)

    qr = "select id_cc, cc_use, n_name  "
    qr += " from brapci_rdf.rdf_concept "
    qr += " inner join brapci_rdf.rdf_literal ON id_n = cc_pref_term"
    qr += f" where cc_class = {IDClass}"
    qr += " and id_cc = cc_use "
    qr += " order by n_name, id_cc"
    row = database.query(qr)
    nx = ''
    ny = ''
    i1 = 0
    i2 = 0
    for n in row:
        ny = n[2]
        i1 = n[0]
        qi = "insert concepts "
        qi += "(n_name, n_ID) "
        qi += " values "
        qi += f"('{ny}',{i1})"
        print(qi)
        quit()
    print("FIM - 205")

def check_remissiva():
    check_use_zero()
    print("202 - Check author remissive forgot")

    dd = 0

    qr = "SELECT id_cc, cc_use, id_n, n_name FROM brapci_rdf.rdf_concept "
    qr += "inner join brapci_rdf.rdf_literal ON cc_pref_term = id_n "
    qr += "inner join brapci_rdf.rdf_data ON ((d_r1 = cc_use) and (d_literal = 0)) "
    qr += "WHERE (cc_use <> id_cc) and (d_r2 > 0) and (cc_use <> 0) "
    row = database.query(qr)

    ID2A = 0
    ID1A = 0

    for reg in row:
        ID2 = reg[0]
        ID1 = reg[1]
        NAME = reg[3]
        if (ID1 != ID1A) and (ID2A != ID2):
            qr = f"select * from brapci_rdf.rdf_data where (d_r1 = {ID2}) or (d_r2 = {ID2}) limit 1"
            row2 = database.query(qr)
            if row2:
                print("Check1 -",ID1,'<=',ID2,NAME)
                mod_data.remicive(ID1,ID2)
                dd = dd + 1
        ID2A = ID2
        ID1A = ID1

    qd = "COMMIT"
    database.update(qd)

    mod_logs.log('TASK_202',dd)
    return ""

def check_duplicate():
    print("200 - Check Duplicate Literal")
    IDClass = mod_class.getClass("Person")

    qr = "select id_cc, cc_use, n_name  "
    qr += " from brapci_rdf.rdf_concept "
    qr += " inner join brapci_rdf.rdf_literal ON id_n = cc_pref_term"
    qr += f" where cc_class = {IDClass}"
    qr += " and id_cc = cc_use "
    qr += " order by n_name, id_cc"

    row = database.query(qr)
    lastName = 'x'
    dd = 0
    for reg in row:
        name = reg[2]
        name = name.replace('-',' ')
        name = name.replace(' de ',' ')
        name = name.replace(' De ',' ')
        name = name.replace(' da ',' ')
        name = name.replace(' Da ',' ')
        name = name.replace(' do ',' ')
        name = name.replace(' Do ',' ')
        name = name.replace(' dos ',' ')
        name = name.replace(' Dos ',' ')
        name = name.replace(' e ',' ')
        name = name.replace('  ',' ')
        name = name.replace('  ',' ')

        IDn1 = reg[0]

        if ((name == lastName) and (name != '::Em Branco::') and (name != '(empty)')):
            print(name)
            remissive(IDn1,IDn2)
            dd = dd + 1
        else:
            reg2 = reg
            lastName = name
            IDn2 = IDn1

    qd = "COMMIT"
    database.update(qd)

    mod_logs.log('TASK_200',dd)

def remissive(ID1,ID2):
    if ID2 < ID1:
        ID3 = ID1
        ID1 = ID2
        ID2 = ID3
    mod_data.remicive(ID1,ID2)

def register_literal(IDC,name):
    if xa.strip() == '':
        return ""

    if not isinstance(xa, str) or not xa.isascii():
        xa = xa.encode('utf-8', errors='ignore').decode('utf-8')

    # Preparação do texto
    if ';' in xa:
        xa = xa[:xa.index(';')]

    xa = troca(xa, ' -', '-')
    xa = troca(xa, '- ', '-')

    while '  ' in xa:
        xa = troca(xa, '  ', ' ')

    xa = xa.strip()

    # Nome Sobrenome
    if ',' in xa:
        partes = xa.split(',', 1)
        xa = f"{partes[1].strip()} {partes[0].strip()}"

    # Divide Nomes
    MN = xa.upper()
    NMT = MN.split(' ')
    NM = [nome for nome in NMT if nome != '']

    # Sobrenomes falsos
    er1 = ['JÚNIOR', 'JUNIOR', 'NETTO', 'NETO', 'SOBRINHO', 'FILHO', 'JR.', 'JR']
    TOT = len(NM)

    if NM[TOT-1] in er1:
        if TOT > 1:
            NM[TOT-2] += f" {NM[TOT-1]}"
            NM.pop()

    # Preposições
    er2 = ['DE', 'DA', 'DO', 'DOS', 'E', 'EM', 'DAS']
    NM2 = [nome for nome in NM if nome not in er2]

    # Minusculas e abreviaturas, considerando acentuação
    Nm = [nome.lower() for nome in NM]
    Ni = [nome[0].upper() for nome in NM]
    Nf = []

    for r in range(len(NM)):
        nome = NM[r]
        # Tratando acentuação corretamente
        nome_formatado = nome[0].upper() + nome[1:].lower()

        # Verifica por hífens ou espaços para formatar corretamente os compostos
        if '-' in nome_formatado:
            partes = nome_formatado.split('-')
            nome_formatado = '-'.join([parte.capitalize() for parte in partes])

        if ' ' in nome_formatado:
            partes = nome_formatado.split(' ')
            nome_formatado = ' '.join([parte.capitalize() for parte in partes])

        # Checa preposições
        if NM[r] in er2:
            nome_formatado = nome.lower()

        Nf.append(nome_formatado)

    # Formatação final conforme o valor de 'xp'
    name = ''
    if xp == '1':  # Sobrenome e Nome
        name = f"{NM[TOT-1]}, " + ' '.join(Nf[:TOT-1])
    elif xp == '2':  # Sobrenome e Nome CURTO
        name = f"{NM2[-1]}, " + ' '.join([f"{n[0]}." for n in NM2[:-1]])
    elif xp == '3':  # Sobrenome e Nome CURTO sem ponto
        name = f"{NM2[-1]} " + ''.join([n[0] for n in NM2[:-1]])
    elif xp == '7':  # Nome e Sobrenome
        name = ' '.join(Nf)
    elif xp == '8':  # Somente primeira letra maiúscula
        name = f"{Nf[0]} " + ' '.join(Nm)
    else:
        print(f"Method {xp} not implemented")
        name = xa

    return name.strip() if len(name.strip()) > 5 else ""
