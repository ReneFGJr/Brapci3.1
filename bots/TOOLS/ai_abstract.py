import re,sys
import database, mod_rdf

def extract_abstract(text,id):
    text = text.replace(chr(10), ' ')
    text = text.replace('.', ';')
    Resumo = extrair_resumo(text)

    tam = len(Resumo)
    if (tam > 100) and (tam < 1500):
        saveAbstract(id,Resumo)
        return Resumo
    sys.exit()

def saveAbstract(id,abstract):
    print("=== Analisando Resumo")
    Prop = 86
    qr = f"select * from brapci_rdf.rdf_data where (d_r1 = {id}) and (d_p = {Prop})"
    row = database.query(qr)
    if row != []:
        print("     Resumo já existe==>",row)
        return

    qr = f"select id_n from brapci_rdf.rdf_literal where n_name = '{abstract}' and n_lang = 'pt'"
    row = database.query(qr)
    if row == []:
        qr = f"insert into brapci_rdf.rdf_literal (n_name,n_lang) values ('{abstract}','pt')"
        database.update(qr)
        qr = f"select id_n from brapci_rdf.rdf_literal where n_name = '{abstract}' and n_lang = 'pt'"
        row = database.query(qr)
    ID = row[0][0]

    mod_rdf.rdf_insert(id,Prop,0,ID)
    print("==>",ID)


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
