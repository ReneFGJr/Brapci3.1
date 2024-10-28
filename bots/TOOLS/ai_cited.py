import sys_io

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
    texto = sys_io.remove_legendas(texto)

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