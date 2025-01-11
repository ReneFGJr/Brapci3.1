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
import sys
import urllib.parse
import subprocess

def harvestingPDF():
    limit = 5
    print(f"Início da coleta de PDF -  {limit} registros")
    qr = "select ID from brapci_elastic.dataset "
    qr += " where "
    qr += "(CLASS = 'Article' or CLASS='Proceeding')"
    qr += " and PDF = 0 "
    qr += " and status = 1 "
    qr += " order by ID DESC "
    qr += f" limit {limit}"
    row = database.query(qr)

    for line in row:
        ID = line[0]
        getPDF(ID)
    print("Fim da coleta de PDF")
    return ""

def validaPDF():
    limit = 10
    print(f"Início da validação de PDF -  {limit} registros")
    prop = mod_class.getClass("hasFileStorage")
    qr = "select d_r1, n_name, id_cc from brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_concept ON d_r2 = id_cc "
    qr += "inner join brapci_rdf.rdf_literal ON cc_pref_term = id_n "
    qr += f" where d_p = {prop}"
    qr += f" limit {limit}"
    print(qr)
    row = database.query(qr)

    print(row[0])
    sys.exit()
    #is_valid_pdf

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

def is_valid_pdf(file_path):
    # Verifica se o arquivo existe
    if not os.path.isfile(file_path):
        print(f"Arquivo não encontrado: {file_path}")
        return False

    try:
        # Abre o arquivo em modo binário e verifica o cabeçalho
        with open(file_path, 'rb') as file:
            header = file.read(4)  # Os primeiros 4 bytes de um arquivo PDF contêm '%PDF'
            if header == b'%PDF':
                print(f"Arquivo é um PDF válido: {file_path}")
                return True
            else:
                print(f"Arquivo não é um PDF válido: {file_path}")
                return False
    except Exception as e:
        print(f"Erro ao verificar o arquivo: {e}")
        return False

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
        if ('revistas.ufpr.br' in link):
            oTXT = read_link_curl(link)
        else:
            oTXT = read_link(link)

        if not isinstance(oTXT, str):
            oTXT = ''

        #************************* citation_pdf_url
        if 'citation_pdf_url' in oTXT:
            methodo = 'citation_pdf_url'
            pattern = r'https?://[^\\s"]*article/download/[^\\s"]*'
            links = re.findall(pattern, oTXT)
            if links != []:
                linkPDF = links[0]

        if linkPDF == '' and 'citation_pdf_url' in oTXT:
            methodo = 'citation_pdf_url'
            pattern = r'<meta\s+name="citation_pdf_url"\s+content="([^"]+)"'
            links = re.findall(pattern, oTXT)
            if links != []:
                linkPDF = links[0]


        # Download
        if linkPDF == '' and 'download' in oTXT:
            methodo = 'pdfJsViewer'
            pattern = r'file=(https%3A%2F%2F.+?)"'
            links = re.findall(pattern, oTXT)
            if links != []:
                linkPDF = links[0]
                linkPDF = decoded_url = urllib.parse.unquote(linkPDF)

        # Download
        if linkPDF == '' and 'obj_galley_link file' in oTXT:
            methodo = 'obj_galley_link'
            pattern = r'<a\s+class="obj_galley_link file"\s+href="([^"]+)"'
            links = re.findall(pattern, oTXT)
            if links != []:
                linkPDF = links[0]

        # Download
        if linkPDF == '' and 'obj_galley_link pdf' in oTXT:
            methodo = 'obj_galley_link'
            pattern = r'<a\s+class="obj_galley_link pdf"\s+href="([^"]+)"'
            links = re.findall(pattern, oTXT)
            if links != []:
                linkPDF = links[0]
                oTXT = read_link(linkPDF)
                if 'pdfJsViewer' in oTXT:
                    methodo = 'pdfJsViewer2'
                    pattern = r'<a\s+href="([^"]+)"[^>]*\bclass="[^"]*\bdownload\b[^"]*"'
                    links = re.findall(pattern, oTXT)
                    if links != []:
                        linkPDF = links[0]
                        linkPDF = decoded_url = urllib.parse.unquote(linkPDF)
                else:
                    linkPDF = ''


    if (linkPDF != ''):
        fileDownload = downloadPDF(linkPDF,ID)

        if fileDownload == "":
            return True

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
        updatePDFdataset(ID,-1)
        return True

def fileName(ID):
    work_number = 0
    id_str = f"{ID:08d}"
    subdirectories = f"{id_str[:2]}/{id_str[2:4]}/{id_str[4:6]}/{id_str[6:]}"
    file_name = f"work_{id_str}#{work_number:05d}.pdf"
    full_path = os.path.join('_repository',subdirectories, file_name)
    return full_path

def downloadPDF(url,ID):
    timeout = 60
    filename = fileName(ID)
    output_path = '../../public/'+filename
    try:
        print(" ")
        print(f"Baixando ...: {ID}, {url}")

        if ('revistas.ufpr.br' in url):
            os.makedirs(os.path.dirname(output_path), exist_ok=True)
            download_pdf_with_curl(url,output_path)
        else:
            response = requests.get(url, stream=True, timeout=timeout, verify=False)
            response.raise_for_status()  # Verifica se houve algum erro no download
            # Cria os diretórios se não existirem
            os.makedirs(os.path.dirname(output_path), exist_ok=True)

            # Escreve o conteúdo do arquivo em chunks para evitar problemas de memória
            with open(output_path, 'wb') as file:
                for chunk in response.iter_content(chunk_size=8192):
                    if chunk:  # Apenas escreve se houver conteúdo
                        file.write(chunk)

        return filename
    except requests.RequestException as e:
        print(f"### Erro ao baixar o arquivo: {e}",output_path)
        return ""

def download_pdf_with_curl(url, output_path):
    try:
        # Comando curl para baixar o arquivo
        command = ["curl", "-o", output_path, url]

        # Executa o comando curl
        result = subprocess.run(command, capture_output=True, text=True, check=True)

        if result.returncode == 0:
            print(f"Arquivo baixado com sucesso: {output_path}")
        else:
            print(f"Erro ao baixar o arquivo. Código de retorno: {result.returncode}")
    except subprocess.CalledProcessError as e:
        print(f"Erro ao executar o comando curl: {e.stderr}")
    except Exception as e:
        print(f"Erro inesperado: {e}")

def read_link_curl(url):
    command = ["curl", "-X", "OPTIONS", "-i", url]
    try:
        result = subprocess.run(command, capture_output=True, text=True, check=True)
        output = result.stdout
        if not output.strip():
            raise ValueError("Curl command returned empty output.")
        return output
    except subprocess.CalledProcessError as e:
        print("Erro ao executar o comando curl:", e.stderr)
        return ""
    except Exception as e:
        print("Unexpected error:", e)
        return ""

def read_link(url, decode=False):
    try:
        response = requests.get(url, timeout=10, verify=False)  # Timeout de 10 segundos

        response.raise_for_status()  # Levanta exceção se o status da resposta não for 200
        content = response.text
        if decode:
            return content.encode('utf-8').decode('unicode_escape')
        return content
    except requests.RequestException as e:
        print(f"Erro ao acessar a URL: {e}")
        return ""

def getPDF(ID):
    print("Processando coleta PDF ",ID)
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
