import re

def converter_para_abnt3(referencia):
    # Expressão regular para capturar os elementos da referência no estilo APA
    regex = r"(?P<autor>.+?) \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?), (?P<volume>\d+)\((?P<numero>\d+)\), (?P<paginas>.+?)\.?(?P<doi> https?://\S+)?"

    # Usando a expressão regular para extrair os componentes
    match = re.match(regex, referencia)

    if match:
        # Extraindo os componentes
        autor = match.group("autor")
        ano = match.group("ano")
        titulo = match.group("titulo")
        fonte = match.group("fonte")
        volume = match.group("volume")
        numero = match.group("numero")
        paginas = match.group("paginas")
        doi = match.group("doi")

        # Convertendo o autor para o formato ABNT
        autores = autor.split(", ")
        autores_abnt = "; ".join([f"{a.split()[-1].upper()} {' '.join(a.split()[:-1])}" for a in autores])

        # Formatando para o padrão ABNT
        referencia_abnt = f"{autores_abnt}. {titulo}. {fonte}, v. {volume}, n. {numero}, p. {paginas}, {ano}."

        # Inclui o DOI se estiver presente
        if doi:
            referencia_abnt += f" Disponível em: {doi.strip()}. Acesso em: [data]."

        return referencia_abnt
    else:
        return "#####"

def converter_para_abnt2(referencia):
    # Expressão regular para capturar os elementos da referência no estilo APA
    regex = r"(?P<autor>.+?) \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?);(?P<volume>\d+)\((?P<numero>\d+)\), (?P<paginas>.+?)\.?(?P<doi> https?://\S+)?"

    # Usando a expressão regular para extrair os componentes
    match = re.match(regex, referencia)

    if match:
        # Extraindo os componentes
        autor = match.group("autor")
        ano = match.group("ano")
        titulo = match.group("titulo")
        fonte = match.group("fonte")
        volume = match.group("volume")
        numero = match.group("numero")
        paginas = match.group("paginas")
        doi = match.group("doi")

        # Convertendo o autor para o formato ABNT
        autores = autor.split(", ")
        autores_abnt = "; ".join([f"{a.split()[1].upper()} {a.split()[0]}" for a in autores])

        # Formatando para o padrão ABNT
        referencia_abnt = f"{autores_abnt}. {titulo}. {fonte}, v. {volume}, n. {numero}, p. {paginas}, {ano}."

        # Inclui o DOI se estiver presente
        if doi:
            referencia_abnt += f" Disponível em: {doi.strip()}. Acesso em: [data]."

        return referencia_abnt
    else:
        return "#####"


def converter_para_abnt(referencia):
    # Expressão regular para capturar os elementos da referência no estilo APA
    regex = r"(?P<autor>.+?) \((?P<ano>\d{4})\). (?P<titulo>.+?)\. (?P<fonte>.+?), (?P<volume_paginas>.+?). (?P<doi>https?://\S+)"

    # Usando a expressão regular para extrair os componentes
    match = re.match(regex, referencia)

    if match:
        # Extraindo os componentes
        autor = match.group("autor")
        ano = match.group("ano")
        titulo = match.group("titulo")
        fonte = match.group("fonte")
        volume_paginas = match.group("volume_paginas")
        doi = match.group("doi")

        # Convertendo o autor para o formato ABNT
        autores = autor.split(", ")
        autores_abnt = f"{autores[1].strip()} {autores[0].strip()}." if len(autores) == 2 else autor
        autores_abnt = autores_abnt.replace(" & ", "; ").upper()

        # Formatando para o padrão ABNT
        referencia_abnt = f"{autores_abnt} {titulo}. {fonte}, v. {volume_paginas}, {ano}. Disponível em: {doi}"
        referencia_abnt = referencia_abnt.replace("'","´")

        return referencia_abnt
    else:
        return "#####"
