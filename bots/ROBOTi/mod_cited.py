import database
import mod_doi
import mod_ai_brapci
import re
import requests
import sys
import mod_cited_abnt
def categorizeCitedByElastic():
    print("Categorize Cited by Elastic")

    qr = "select ID, id_ds from brapci_elastic.dataset "
    qr += " where cited = -1 "
    qr += " order by ID desc "
    qr += " limit 10000 "
    row = database.query(qr)

    for line in row:
        ID = line[0]
        id_ds = line[1]
        url = "https://cip.brapci.inf.br/api/brapci/get/v1/"+str(ID)

        # Fazer uma solicitação GET para a API
        response = requests.get(url)

        # Verificar se a solicitação foi bem-sucedida (código de status 200)
        if response.status_code == 200:
            data = response.json()
            cites_value = data.get('cites')
            # Verificando o número de cites
            if cites_value:
                number_of_cites = len(cites_value)
            else:
                number_of_cites = 0
            print("=>",ID,"OK",number_of_cites)
        else:
            print("=>",ID,"ERRO")
            number_of_cites = -1

        qu = "update brapci_elastic.dataset set cited = "+str(number_of_cites)+" where id_ds = "+str(id_ds)
        database.update(qu)

def categorizeCited():
    print("Categorizando Citações")
    qr = "select id_ca, ca_text from brapci_cited.cited_article "
    qr += " where ca_status = 0 "
    qr += " and (ca_tipo = 0 or ca_tipo is null) "
    qr += " order by id_ca "
    qr += " limit 100 "
    row = database.query(qr)

    for line in row:
        message = "Coloque na norma da ABNT."
        message += 'Com base nas categorias: Identifique o tipo dessa fonte e o ano da publicação, os tipos são: '
        message += '"Não Identificado" como "NI"; "Artigos" como "ARTICLE"; "Livro" como "BOOK"; '
        message += '"Cap. Livro" como "BOOK.CAP"; "Anais de eventos" como "PROCEEDINGS"; "Tese" como "THESE"; '
        message += '"Dissertação" como "DISSERTATION"; "TCC" como "TCC"; "Link de internet" como "LINK"; '
        message += '"Journal Diário" como "NEWSPAPPER"; "Filme" como "MOVIE"; '
        message += '"Revista semanal (Entreterimento)" como "MAGAZINE"; "Leis" como "LAW"; '
        message += '"Relatórios" como "REPORT"; "Normas técnicas" como "STANDART"; "Entrevista" como "INTERVIEW"; '
        message += '"Software" como "SOFTWARE"; "Patentes" como "PATENT"; "Base de dados" como "DATABASE"; '
        message += '"Notas de Pesquisa / Outros" como "OTHER"; "Nulo - Null" como "NULL". '
        message += 'Caso haja dúvida na referência marque o grau de certeza igual a -1. '
        message += 'Quanto mais adequado a referência, maior será o seu grau de certeza. '
        message += 'Seja Claro e Específico na analise. '
        message += 'Responda apenas o tipo, ponto e virgula e o ano da publicação, ponto e virgula e um valor entre 0 a 9 para o grau de certeza. '
        message += 'A referência é: '
        message += line[1]
        ID = line[0]

        print("==========================================================")
        print(line[1])
        print("Human: ",message)
        print("Bot: ")
        RSP = mod_ai_brapci.chat(message)

        MSG = RSP['message']
        DDD = MSG.split(';')

        print("Resposta",RSP)

        tipo = DDD[0]
        year = DDD[1]
        nivel = DDD[2]

        #****************************************************************************************************/
        #Valida o tipo
        qr = f"select * from brapci_cited.cited_type where ct_type = '{tipo}'"
        row = database.query(qr)

        if row != []:
            tipoX = row[0]
            print("TIPO=>",tipoX)
            tipoX = tipoX[0]
            qu = f"update brapci_cited.cited_article set ca_status = 1,  ca_tipo = '{tipoX}', ca_year = '{year}', ca_ai = {nivel} where id_ca = {ID}"
            database.update(qu)
        else:
            qu = f"update brapci_cited.cited_article set ca_status = 9,  ca_tipo = '0', ca_ai = 1 where id_ca = {ID}"
            database.update(qu)
            print(qu)
            print("########################### ERRO")
            print(tipo,year,nivel)
            print(row)

def cited():
    print("DOI - Localizando DOI nos metadados")
    qr = "select id_n, d_r1, n_name from brapci_rdf.rdf_literal "
    qr += "inner join brapci_rdf.rdf_data ON id_n = d_literal "
    qr += "where n_name like '%/*ref*/%'"
    row = database.query(qr)

    for item in row:
        ID = item[1]
        IDn = item[0]
        REF = item[2]

        REF = REF.replace('/*ref*/','').strip()
        register(ID,REF)
        removeLiteral(ID,IDn)
        print(ID,IDn,REF)

def longCited():
    print("Marcando citações muitos longas")
    qu = "UPDATE "
    qu += "brapci_cited.cited_article "
    qu += " set ca_status = 9 "
    qu += "WHERE LENGTH(ca_text) > 1000;"
    database.update(qu)

def locate():
    print("DOI - Localizando DOI nas referencias")
    qr = "select * from brapci_cited.cited_article "
    qr += " where (ca_doi = '' or ca_doi is null) "
    qr += " and ca_text like '%10.%' "
    qr += " and ca_text like '%doi%' "
    qr += "limit 1000  "
    row = database.query(qr)

    for line in row:
        cite = line[12]
        DOI = mod_doi.encontrar_doi(cite)

        if (DOI != ''):
            print("==>DOI",DOI)
            id = line[0]
            update_cited_doi(id,DOI)

def update_cited_doi(id,DOI):
    qu = "update brapci_cited.cited_article "
    qu += f" set ca_doi = '{DOI}' "
    qu += f" where id_ca = {id}"
    database.update(qu)

def removeLiteral(ID,IDn):
    qr = f"delete from brapci_rdf.rdf_data where d_r1 = {ID} and d_literal = {IDn} "
    database.update(qr)

    qr = f"select * from brapci_rdf.rdf_data where d_literal = {IDn} "
    row = database.query(qr)
    if not row:
        print("Deletar")
        qr = f"delete from brapci_rdf.rdf_literal where id_n = {IDn} "
        database.update(qr)

######################################################## categorizeBook
def categorizeBook():
    print("Categorizando livros")
    qr = "select id_ca, ca_text from brapci_cited.cited_article where ca_tipo = 0 and ca_status = 0"
    row = database.query(qr)

    for line in row:
        citacao = line[1]
        ID = line[0]
        if identificacao_basica(citacao):
            print("Basico",citacao)
            update_type(ID,2)
        else:
            if (identificao_cidade(citacao)):
                print("Cidade",citacao)
                update_type(ID,2)
            elif (identificacao_capitulo(citacao)):
                print("Capitulo",citacao)
                update_type(ID,2)
            elif(identificacao_organizado(citacao)):
                print("Organizado",citacao)
                update_type(ID,2)

########################################################### ABNT
######################################################## categorizeBook
def refatureABNT():
    print("Convertendo para ABNT")
    qr = "select id_ca, ca_text from brapci_cited.cited_article where ca_tipo = 0 and ca_status = 0 and "
    qr += "((ca_text like '%(20%') or (ca_text like '%(19%')) "
    row = database.query(qr)
    for line in row:
        id = line[0]
        ref = line[1]
        conv = mod_cited_abnt.converter_para_abnt(ref)

        if (conv != '#####'):
            print("Original:",ref)
            print("Convertido:",conv)
            print("============================")
            update_cited(id,conv)


def remove_spaces():
    print("Removendo espacos entre dois pontos")
    qr = "select id_ca,ca_text from brapci_cited.cited_article where ca_text like '% :%'"
    row = database.query(qr)
    for line in row:
        text = line[1]
        text = text.replace(' :',':')
        text = text.replace("'","´")
        ID = line[0]
        update_cited(ID,text)
        print(".",end='')

def recupera_cidades():
    lista_cidades = []
    qr = "select city_name from brapci_cited.city_place where 1"
    row = database.query(qr)
    for line in row:
        lista_cidades.append(line[0])

    return lista_cidades
def identificao_cidade(reference: str) -> bool:
    lista_cidades = recupera_cidades()

    cidades_encontradas = []
    for cidade in lista_cidades:
        if cidade+':' in reference:  # Verifica se a cidade está na string (comparação direta)
            cidades_encontradas.append(cidade)

    return True if cidades_encontradas else False

def update_cited(ID,cited):
    qu = f"update brapci_cited.cited_article set ca_text = '{cited}' where id_ca = {ID}"
    database.update(qu)

def update_type(ID,type):
    qu = f"update brapci_cited.cited_article set ca_tipo = '{type}' where id_ca = {ID}"
    print(f"Update Type {type} in {ID}")
    database.update(qu)

def identificacao_capitulo(reference: str) -> bool:
    # Expressão regular para detectar o formato de capítulo de livro
    pattern = r"In:\s[A-Z][a-z]+,\s[A-Z][a-z]+(\.\s)?[A-Z][a-z]+.*\.\s[A-Z][a-z]+:\s[A-Z][a-z]+,\s\d{4}\.\sp\.\s\d+-\d+"

    # Verifica se a referência contém o padrão de capítulo de livro
    match = re.search(pattern, reference)

    if match:
        return True
    else:
        return False

def identificacao_autor_unico(referencia):
    # Expressão regular para identificar uma referência de livro de autoria única
    padrao_autor_unico = re.compile(r'^[A-Z][a-z]+, [A-Z][a-z]+\. .+\. [A-Z][a-z]+: .+, \d{4}\.$')

    # Verifica se a referência corresponde ao padrão de livro de autoria única
    if padrao_autor_unico.match(referencia):
        return True
    else:
        return False



def identificacao_basica(referencia):
    # Expressão regular para identificar uma referência de livro
    padrao_livro = re.compile(r'^[A-Z]+, [A-Z]\..+\. [A-Z].+:.+, \d{4}\.$')

    # Verifica se a referência corresponde ao padrão de livro
    if padrao_livro.match(referencia):
        return True
    else:
        return False

def identificacao_organizado(referencia):
    # Expressão regular para identificar uma referência de livro organizado
    padrao_organizado = re.compile(r'^[A-Z][a-z]+, [A-Z][a-z]+\..+In: [A-Z][a-z]+, [A-Z][a-z]+(\., [A-Z][a-z]+\.)?\s*\(Ed\.|Eds\.\).+:.+, \d{4}\. p\. \d{1,4}-\d{1,4}\.$')

    # Verifica se a referência corresponde ao padrão de livro organizado
    if padrao_organizado.match(referencia):
        return True
    else:
        return False


def identificar_livro(citacao):
    # Expressão regular para capturar autor, título, cidade, editora e ano
    pattern = r"(?P<autor>^[A-Z][A-Z\s]+, [A-Z][a-zçáéíóúãõâêîôû]+\.?)\s(?P<titulo>.+?)\.\s(?P<cidade>[A-Za-z]+):\s(?P<editora>[^,]+),\s(?P<ano>\d{4})\."

    match = re.search(pattern, citacao)

    if match:
        return match.groupdict()  # Retorna um dicionário com os grupos capturados
    else:
        return None  # Caso não encontre, retorna None

########################################################### Categorização Year
def categorizeYear():
    print("Categorizando ano de produção")
    qr = "select id_ca, ca_text from brapci_cited.cited_article where ca_year = 0 and ca_status < 9"
    row = database.query(qr)

    for line in row:
        ID = line[0]
        ref = line[1]
        year = year_identify(ref)
        qu = f"update brapci_cited.cited_article set ca_year = '{year}' where id_ca = {ID}"
        print(f"Update Year {year} in {ID}")
        database.update(qu)

def year_identify(referencia):
    # Expressão regular para identificar o ano
    match = re.search(r'(\d{4})', referencia)
    if match:
        return match.group(1)  # Retorna o ano encontrado
    else:
        return 9999  # Caso não encontre, retorna None

def delete(ID):
    qr = "delete from brapci_cited.cited_article where ca_rdf = "+ID
    row = database.query(qr)


def register(ID,REF):
    qr = "select * from brapci_cited.cited_article "
    qr += f" where ca_text = '{REF}' "
    qr += f" and ca_rdf = {ID}"
    row = database.query(qr)
    if not row:
        qi = "insert into brapci_cited.cited_article "
        qi += "(ca_rdf, ca_text, ca_status) "
        qi += " values "
        qi += f"('{ID}','{REF}',0)"
        try:
            database.insert(qi)
        except Exception as e:
            print("ERRO CITED",e)
