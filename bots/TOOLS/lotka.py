from collections import Counter
import sys
import openpyxl
from openpyxl import Workbook
from openpyxl.chart import BarChart, Reference

def calcular_lei_de_lotka(lista_de_artigos):
    """
    Calcula a Lei de Lotka a partir de uma lista de artigos.

    Args:
        lista_de_artigos (list): Lista de strings, onde cada string contém autores separados por ponto e vírgula (;).

    Returns:
        dict: Frequência de autores e distribuição de Lotka.
    """
    # Dividir os autores de cada artigo e criar uma lista única de autores
    autores = []
    for artigo in lista_de_artigos:
        autores.extend([autor.strip() for autor in artigo.split(';')])

    # Contar o número de publicações por autor
    contagem_autores = Counter(autores)

    # Contar a frequência de publicações (quantos autores publicaram x artigos)
    frequencia_publicacoes = Counter(contagem_autores.values())

    # Ordenar os dados por número de publicações
    distribuicao_lotka = dict(sorted(frequencia_publicacoes.items()))

    return contagem_autores, distribuicao_lotka

def salvar_resultados_xlsx(contagem_autores, distribuicao_lotka, nome_arquivo):
    """
    Salva os resultados da Lei de Lotka em um arquivo XLSX, incluindo gráficos em abas separadas.

    Args:
        contagem_autores (dict): Contagem de artigos por autor.
        distribuicao_lotka (dict): Distribuição da Lei de Lotka.
        nome_arquivo (str): Nome do arquivo XLSX.
    """
    # Criar um workbook
    wb = Workbook()

    # Adicionar aba para contagem de autores
    ws_autores = wb.active
    ws_autores.title = "Contagem de Autores"
    ws_autores.append(["Autor", "Número de Artigos"])
    for autor, quantidade in contagem_autores.items():
        ws_autores.append([autor, quantidade])

    # Criar gráfico para contagem de autores
    chart_autores = BarChart()
    chart_autores.title = "Contagem de Autores"
    chart_autores.x_axis.title = "Autores"
    chart_autores.y_axis.title = "Número de Artigos"
    data = Reference(ws_autores, min_col=2, min_row=1, max_row=len(contagem_autores) + 1)
    labels = Reference(ws_autores, min_col=1, min_row=2, max_row=len(contagem_autores) + 1)
    chart_autores.add_data(data, titles_from_data=True)
    chart_autores.set_categories(labels)
    ws_autores.add_chart(chart_autores, "D1")

    # Adicionar aba para distribuição de Lotka
    ws_lotka = wb.create_sheet(title="Distribuição de Lotka")
    ws_lotka.append(["Número de Artigos", "Número de Autores"])
    for num_artigos, num_autores in distribuicao_lotka.items():
        ws_lotka.append([num_artigos, num_autores])

    # Criar gráfico para distribuição de Lotka
    chart_lotka = BarChart()
    chart_lotka.title = "Distribuição de Lotka"
    chart_lotka.x_axis.title = "Número de Artigos"
    chart_lotka.y_axis.title = "Número de Autores"
    data_lotka = Reference(ws_lotka, min_col=2, min_row=1, max_row=len(distribuicao_lotka) + 1)
    labels_lotka = Reference(ws_lotka, min_col=1, min_row=2, max_row=len(distribuicao_lotka) + 1)
    chart_lotka.add_data(data_lotka, titles_from_data=True)
    chart_lotka.set_categories(labels_lotka)
    ws_lotka.add_chart(chart_lotka, "D1")

    # Salvar o arquivo Excel
    wb.save(nome_arquivo)

if __name__ == "__main__":
    # Verificar se o número correto de argumentos foi fornecido
    if len(sys.argv) != 2:
        print("Uso: python3 lotka.py <arquivo_entrada>")
        sys.exit(1)

    # Obter o arquivo de entrada
    arquivo_entrada = sys.argv[1]
    arquivo_saida = arquivo_entrada.replace('.txt', '.xlsx')

    # Ler o arquivo de entrada
    try:
        with open(arquivo_entrada, mode='r', encoding='utf-8') as file:
            lista_de_artigos = [linha.strip() for linha in file.readlines() if linha.strip()]
    except FileNotFoundError:
        print(f"Erro: O arquivo {arquivo_entrada} não foi encontrado.")
        sys.exit(1)

    # Calcular a Lei de Lotka
    contagem_autores, distribuicao_lotka = calcular_lei_de_lotka(lista_de_artigos)

    # Salvar os resultados em um arquivo XLSX
    salvar_resultados_xlsx(contagem_autores, distribuicao_lotka, arquivo_saida)

    print(arquivo_saida)
