import fitz  # Importa PyMuPDF
import re
import os
import database
import mod_data
import mod_class
import mod_concept
import requests
import re
import mod_literal
import mod_GoogleTranslate

def harvestingPDF():
    qr = "select ID from brapci_elastic.dataset "
    qr += " where "
    qr += "(CLASS = 'Article' or CLASS='Proceeding')"
    qr += " and PDF = 0 "
    qr += " and ID < 328661 "
    qr += " order by ID DESC "
    qr += " limit 1"
    row = database.query(qr)

    for line in row:
        ID = line[0]
        getPDF(ID)
    print("Fim da coleta de PDF")
    return ""

def existPDF(ID):
    prop = mod_class.getClass("hasFileStorage")
    qr = "select * from brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_concept ON d_r2 = id_cc "
    qr += f" where d_r1 = {ID} and d_p = {prop}"
    row = database.query(qr)
    if row == []:
        return False
    else:
        return True

def updatePDFdataset(ID,status):
    qu = "update brapci_elastic.dataset "
    qu += f" set PDF = {status} "
    qu += f" where ID = {ID} "
    database.update(qu)

def download_methods(row):
    linkPDF = ''
    link = row[0]
    ID = row[1]

    link = link.replace('/XIXENANCIB/','/XIX_ENANCIB/')
    link = link.replace('/xviiienancib/','/XVIII_ENANCIB/')
    link = link.replace('http://www.periodicos.ufpb.br/ojs/','https://www.pbcib.com/')
    link = link.replace('//seer.ufs.br/index.php/','//periodicos.ufs.br/')

    if ('rev-ib.unam.mx' in link):
        link = link
    else:
        link = link.replace('http://','https://')

    methodo = ''
    if 'article/view' in link:
        oTXT = read_link(link)

        #************************* citation_pdf_url
        if 'citation_pdf_url' in oTXT:
            methodo = 'citation_pdf_url'
            pattern = r'https?://[^\\s"]*article/download/[^\\s"]*'
            links = re.findall(pattern, oTXT)
            if links != []:
                linkPDF = links[0]
        else:
            #updatePDFdataset(ID,-1)
            print("SKIP")


    if (linkPDF != ''):
        print("PDF LINK",linkPDF)
        fileDownload = downloadPDF(linkPDF,ID)

        # Cria valor literal
        IDn = mod_literal.register(fileDownload,'nn')
        IDClass = mod_class.getClass("FileStorage")

        # Cria o FileStorage
        IDc = mod_concept.register(IDClass,IDn)

        print("==========================================")
        print("... ID         : ",ID)
        print("... Link       : ",linkPDF)
        print("... Filename   : ",fileDownload)
        print("... Methodo    : ",methodo)
        print("... ID Class   : ",IDClass)
        print("... ID literal : ",IDn)
        print("... ID concept : ",IDc)

        # Associa o FileStorage ao Trabalho
        #IDprop = mod_class.getClass("hasFileStorage")
        mod_data.register(ID,"hasFileStorage",IDc,0)

        #Atualiza sistema
        updatePDFdataset(ID,1)
        return False
    else:
        return True

def fileName(ID):
    work_number = 0
    id_str = f"{ID:08d}"
    subdirectories = f"{id_str[:2]}/{id_str[2:4]}/{id_str[4:6]}/{id_str[6:]}"
    file_name = f"work_{id_str}#{work_number:05d}.pdf"
    full_path = os.path.join('_repository',subdirectories, file_name)
    return full_path

def downloadPDF(url,ID):
    filename = fileName(ID)
    output_path = '../../public/'+filename
    try:
        print(f"Baixando: {url}")
        response = requests.get(url, stream=True)
        response.raise_for_status()  # Verifica se houve algum erro no download

        # Cria os diretórios se não existirem
        os.makedirs(os.path.dirname(output_path), exist_ok=True)

        # Escreve o conteúdo do arquivo em chunks para evitar problemas de memória
        with open(output_path, 'wb') as file:
            for chunk in response.iter_content(chunk_size=8192):
                if chunk:  # Apenas escreve se houver conteúdo
                    file.write(chunk)

        print(f"Arquivo salvo em: {output_path}")
        return filename
    except requests.RequestException as e:
        print(f"Erro ao baixar o arquivo: {e}")
        return ""

def read_link(url, decode=False):
    try:
        response = requests.get(url, timeout=10)  # Timeout de 10 segundos
        response.raise_for_status()  # Levanta exceção se o status da resposta não for 200
        content = response.text
        if decode:
            return content.encode('utf-8').decode('unicode_escape')
        return content
    except requests.RequestException as e:
        print(f"Erro ao acessar a URL: {e}")
        return ""

def getPDF(ID):
    ePDF = existPDF(ID)
    if ePDF:
        print(ID," ... PDF já existe")
        return ""

    prop1 = mod_class.getClass("hasRegisterId")
    prop2 = mod_class.getClass("hasUrl")
    qr = "select n_name, d_r1 from brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_literal ON d_literal = id_n "
    qr += f" where d_r1 = {ID} and (d_p = {prop1} OR d_p = {prop2})"

    row = database.query(qr)

    loop = True
    for line in row:
        if loop:
            loop = download_methods(line)
    return loop

def convert(ID):
    prop = mod_class.getClass("hasFileStorage")
    qr = "select n_name, n_lang from brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_concept ON d_r2 = id_cc "
    qr += "inner join brapci_rdf.rdf_literal ON cc_pref_term = id_n "
    qr += f" where d_r1 = {ID} and d_p = {prop}"
    row = database.query(qr)

    for ln in row:
        file = '/data/Brapci3.1/public/' + ln[0]
        print(file)
        if os.path.isfile(file):
            extrair_texto_pdf(file)
            print("  OK")
        else:
            print("  FILE NOT FOUND")


# Função para extrair texto do PDF
def extrair_texto_pdf(caminho_arquivo):
    texto = ""
    with fitz.open(caminho_arquivo) as doc:
        for pagina in doc:
            texto += pagina.get_text()

    caminho_arquivo_txt = caminho_arquivo.replace('.pdf','.txt')

    with open(caminho_arquivo_txt, 'w', encoding='utf-8') as arquivo:
        arquivo.write(texto)
        print("Convertido ",caminho_arquivo_txt)

    return texto
