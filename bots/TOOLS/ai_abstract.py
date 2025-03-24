import re,sys
import database

def extract_abstract(text,id):
    text = text.replace(chr(10), ' ')
    text = text.replace('.', ';')
    Resumo = extrair_resumo(text)

    tam = len(Resumo)
    if (tam > 100) and (tam < 1500):
        print("===>",Resumo)
        saveAbstract(id,Resumo)
        return Resumo
    sys.exit()

def saveAbstract(id,abstract):
    qr = f"select * from brapci_rdf.rdf_literal where n_name = '{abstract}' and n_lang = 'pt'"
    row = database.query(qr)
    if row == []:
        qr = f"insert into brapci_rdf.rdf_literal (n_name,n_lang) values ('{abstract}','pt')"
        #database.update(qr)
        qr = f"select * from brapci_rdf.rdf_literal where n_name = '{abstract}' and n_lang = 'pt'"
        #row = database.query(qr)
    print("==>",row)

def extrair_resumo(texto):
    # Expressão regular para capturar o conteúdo entre "Resumo:" e "PALAVRAS-CHAVE"
    padrao = r"Resumo:(.*?)(?:PALAVRAS[- ]CHAVE|Palavras[- ]chave|Abstract:)"

    # Busca ignorando quebras de linha
    correspondencia = re.search(padrao, texto, re.DOTALL | re.IGNORECASE)

    if correspondencia:
        resumo = correspondencia.group(1).strip()
        return resumo
    else:
        return "Resumo não encontrado."


#################
#resumo_extraido = extrair_resumo(conteudo)
