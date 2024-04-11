

def remover_linhas_iguais_arquivo(caminho_arquivo):
    """
    Remove linhas duplicadas de um arquivo de texto, preservando a ordem original das linhas únicas.
    Esta função lê o arquivo, remove as duplicatas, e reescreve o arquivo com as linhas únicas.

    :param caminho_arquivo: Caminho do arquivo de texto a ser processado.
    """
    with open(caminho_arquivo, 'r', encoding='utf-8') as arquivo:
        linhas = arquivo.readlines()

    linhas_unicas = []
    conjunto_linhas = set()
    for linha in linhas:
        if linha not in conjunto_linhas:
            conjunto_linhas.add(linha)
            linhas_unicas.append(linha)

    with open(caminho_arquivo+'2', 'w', encoding='utf-8') as arquivo:
        arquivo.writelines(linhas_unicas)

file = '/data/Brapci3.1/public/_repository/1/2018/09/oai_febab_periodicos_emnuvens_com_br_article_449#00001.txt'
remover_linhas_iguais_arquivo(file)
print(file)