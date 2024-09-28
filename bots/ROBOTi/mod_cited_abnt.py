import re

def converter_para_abnt(referencia):
    # Expressão regular para capturar os elementos da referência no estilo APA
    regexes = [
        # Expressões regulares para diferentes formatos
        r"(?P<autor>.+?) \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?);(?P<volume>\d+)\((?P<numero>\d+)\), (?P<paginas>.+?)\. doi: (?P<doi>\S+)",
        r"(?P<autor>.+?) \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?), (?P<volume>\d+)\((?P<numero>\d+)\), (?P<paginas>.+?)\.?(?P<doi> https?://\S+)?",
        r"(?P<autor>.+?) \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?);(?P<volume>\d+)\((?P<numero>\d+)\), (?P<paginas>.+?)\.?(?P<doi> https?://\S+)?",
        r"(?P<autor>.+?) \((?P<ano>\d{4})\). (?P<titulo>.+?)\. (?P<fonte>.+?), (?P<volume_paginas>.+?). (?P<doi>https?://\S+)",
        r"(?P<autor>.+?)\. \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?); (?P<volume>\d+), (?P<paginas>e?\d+-e?\d+)\. doi: (?P<doi>\S+)",
        r"(?P<autor>.+?)\. \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?), (?P<volume>.+?)\((?P<suplemento>.+?)\), (?P<paginas>S?\d+-S?\d+)\. doi: (?P<doi>\S+)",
        r"(?P<autor>.+?)\. \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?); (?P<volume>\d+) \((?P<numero>\d+)\), (?P<paginas>\d+-\d+)\.",
        r"(?P<autor>.+?)\. \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?); (?P<volume>\d+)\((?P<numero>\d+)\), (?P<paginas>\d+-\d+)\."
    ]

    for regex in regexes:
        match = re.match(regex, referencia)
        if match:
            # Extraindo os componentes comuns
            autor = match.group("autor")
            ano = match.group("ano")
            titulo = match.group("titulo")
            fonte = match.group("fonte")
            doi = match.group("doi") if "doi" in match.groupdict() else None

            # Componentes específicos com controle de existência
            volume = match.group("volume") if "volume" in match.groupdict() else None
            numero = match.group("numero") if "numero" in match.groupdict() else None
            paginas = match.group("paginas") if "paginas" in match.groupdict() else None
            volume_paginas = match.group("volume_paginas") if "volume_paginas" in match.groupdict() else None

            # Convertendo o autor para o formato ABNT
            autores = autor.split(", ")
            autores_abnt = "; ".join([f"{a.split()[-1].upper()} {' '.join(a.split()[:-1])}" for a in autores])

            # Construindo a referência no formato ABNT com base nos elementos encontrados
            referencia_abnt = f"{autores_abnt}. {titulo}. {fonte}"

            # Adicionando volume, número e páginas, se disponíveis
            if volume and numero and paginas:
                referencia_abnt += f", v. {volume}, n. {numero}, p. {paginas}"
            elif volume_paginas:
                referencia_abnt += f", v. {volume_paginas}"

            # Adicionando ano e DOI, se disponível
            referencia_abnt += f", {ano}."
            if doi:
                referencia_abnt += f" Disponível em: {doi.strip()}. Acesso em: [data]."

            return referencia_abnt

    return "#####"
