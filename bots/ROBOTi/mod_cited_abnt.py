import re

def converter_para_abnt(referencia):
    # Expressão regular para capturar os elementos da referência no estilo APA e outros formatos
    regexes = [
        # Expressões regulares para diferentes formatos
        r"(?P<autor>.+?) \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?);(?P<volume>\d+)\((?P<numero>\d+)\), (?P<paginas>.+?)\. doi: (?P<doi>\S+)",
        r"(?P<autor>.+?) \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?), (?P<volume>\d+)\((?P<numero>\d+)\), (?P<paginas>.+?)\.?(?P<doi> https?://\S+)?",
        r"(?P<autor>.+?) \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?);(?P<volume>\d+)\((?P<numero>\d+)\), (?P<paginas>.+?)\.?(?P<doi> https?://\S+)?",
        r"(?P<autor>.+?) \((?P<ano>\d{4})\). (?P<titulo>.+?)\. (?P<fonte>.+?), (?P<volume_paginas>.+?). (?P<doi>https?://\S+)",
        r"(?P<autor>.+?)\. \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?); (?P<volume>\d+), (?P<paginas>e?\d+-e?\d+)\. doi: (?P<doi>\S+)",
        r"(?P<autor>.+?)\. \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?), (?P<volume>.+?)\((?P<suplemento>.+?)\), (?P<paginas>S?\d+-S?\d+)\. doi: (?P<doi>\S+)",
        r"(?P<autor>.+?)\. \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?); (?P<volume>\d+) \((?P<numero>\d+)\), (?P<paginas>\d+-\d+)\.",
        r"(?P<autor>.+?)\. \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?); (?P<volume>\d+)\((?P<numero>\d+)\), (?P<paginas>\d+-\d+)\.",
        r"(?P<autor>.+?)\. (?P<titulo>.+?)\. (?P<fonte>.+?)\. (?P<ano>\d{4});(?P<volume>\d+)\((?P<numero>\d+)\):(?P<paginas>\d+-\d+)\. doi: (?P<doi>\S+)",
        r"(?P<autor>.+?)\. \((?P<ano>\d{4})\)\. (?P<titulo>.+?)\. (?P<fonte>.+?), (?P<volume>\d+)\((?P<numero>\d+)\), (?P<paginas>\d+-\d+)\.",
        # Novo formato para o estilo fornecido:
        r"(?P<autor>.+?), \((?P<ano>\d{4})\) (?P<titulo>.+?)\. (?P<local>.+?): (?P<editora>.+)\."
    ]

    for regex in regexes:
        match = re.match(regex, referencia)
        if match:
            # Extraindo os componentes comuns
            autor = match.group("autor")
            ano = match.group("ano")
            titulo = match.group("titulo")

            # Componentes específicos com controle de existência
            local = match.group("local") if "local" in match.groupdict() else None
            editora = match.group("editora") if "editora" in match.groupdict() else None
            volume = match.group("volume") if "volume" in match.groupdict() else None
            numero = match.group("numero") if "numero" in match.groupdict() else None
            paginas = match.group("paginas") if "paginas" in match.groupdict() else None
            doi = match.group("doi") if "doi" in match.groupdict() else None

            # Convertendo o autor para o formato ABNT
            autores = autor.split(", ")
            autores_abnt = "; ".join([f"{a.split()[-1].upper()} {' '.join(a.split()[:-1])}" for a in autores])

            # Construindo a referência no formato ABNT com base nos elementos encontrados
            referencia_abnt = f"{autores_abnt}. {titulo}."

            # Adicionando local e editora, se disponíveis
            if local and editora:
                referencia_abnt += f" {local}: {editora}"

            # Adicionando volume, número e páginas, se disponíveis
            if volume and numero and paginas:
                referencia_abnt += f", v. {volume}, n. {numero}, p. {paginas}"

            # Adicionando ano e DOI, se disponível
            referencia_abnt += f", {ano}."
            if doi:
                referencia_abnt += f" Disponível em: {doi.strip()}."

            return referencia_abnt

    return "#####"
