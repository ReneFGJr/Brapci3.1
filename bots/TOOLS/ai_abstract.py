import re,sys

def extract_abstract(text,id):
    text = text.replace(chr(10), ' ')
    text = text.replace('.', ';')
    Resumo = extrair_resumo(text)
    print(Resumo)
    sys.exit()

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
