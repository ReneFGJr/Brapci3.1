import re
import sys_io
import sys

def preparar_referencias(texto):
    # Substitui quebras de linha que não têm um espaço após para simplificar
    texto = texto.replace('\n', ' ')
    # Separa as referências por padrão em pontos finais que antecedem o próximo nome
    referencias = texto.split('. ')

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

def extrair_metodologia(texto):
    Tini = ''
    Tfim = ''
    if 'PROCEDIMENTOS METODOLÓGICOS' in texto:
        Tini = 'PROCEDIMENTOS METODOLÓGICOS'
    if 'METODOLOGIA' in texto:
        Tini = 'PROCEDIMENTOS METODOLÓGICOS'

def locale_referencias_type(text):
    tp = ['REFERÊNCIAS']

    # Divide o texto em linhas
    linhas = sys_io.separar_por_linhas(text)

    # Percorre cada linha
    for linha in linhas:
        for wd in tp:
            # Verifica se a palavra-chave está na linha
            if wd in linha:
                return wd.strip()
    return ""

def extrair_referencias(texto):
    start_section = locale_referencias_type(texto)
    texto = remove_legendas(texto)

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

    ref = preparar_referencias(ref)

    return ref.strip()

def remove_legendas(texto):
    textO = ''
    # Divide o texto em linhas

    linhas = sys_io.separar_por_linhas(texto)

    # Remove números de cada linha
    linhasO = [remover_numeros(linha) for linha in linhas]

    # Dicionário para armazenar as ocorrências e os índices
    contagem_indices = {}
    for i, linha in enumerate(linhasO):
        if linha in contagem_indices:
            contagem_indices[linha].append(i)
        else:
            contagem_indices[linha] = [i]

    # Filtra os índices das linhas que estão duplicadas
    indices_duplicados = []
    for indices in contagem_indices.values():
        if len(indices) > 1:  # Se a linha aparece mais de uma vez
            indices_duplicados.extend(indices)

    # Remove as linhas duplicadas a partir dos índices, em ordem decrescente
    for idln in sorted(indices_duplicados, reverse=True):
        if not '.' in linhas[idln]:
            linhas.pop(idln)

    for ln in linhas:
        textO += ln + '\n'

    return textO

#Remove números

def remover_numeros(texto):
    return ''.join([char for char in texto if not char.isdigit()])


def extrair_secoes_method_01(texto):
    secoes = {
        "Título": re.search(r"Artigo\s+(.*)", texto, re.IGNORECASE),
        "Palavras-chave": re.search(r"PALAVRAS-CHAVE\s*(.*)", texto, re.IGNORECASE),
        "Resumo": re.search(r"RESUMO\s*(.*?)(?=\nPALAVRAS-CHAVE)", texto, re.IGNORECASE | re.DOTALL),
        "Introdução": re.search(r"INTRODUÇÃO\s*(.*?)(?=\nPROCEDIMENTOS METODOLÓGICOS)", texto, re.IGNORECASE | re.DOTALL),
        "Metodologia": re.search(r"PROCEDIMENTOS METODOLÓGICOS\s*(.*?)(?=\nRESULTADOS)", texto, re.IGNORECASE | re.DOTALL),
        "Resultados": re.search(r"RESULTADOS\s*(.*?)(?=\nCONCLUSÃO)", texto, re.IGNORECASE | re.DOTALL),
        "Conclusão": re.search(r"CONCLUSÃO\s*(.*?)(?=\nREFERÊNCIAS)", texto, re.IGNORECASE | re.DOTALL),
        "ReferencialTeórico": re.search(r"REFERENCIAL TEÓRICO\s*(.*?)(?=\nMETODOLOGIA)", texto, re.IGNORECASE | re.DOTALL)
    }

    extracoes = {}
    for secao, conteudo in secoes.items():
        if conteudo:
            extracoes[secao] = conteudo.group(1).strip()
        else:
            extracoes[secao] = None

    return extracoes
