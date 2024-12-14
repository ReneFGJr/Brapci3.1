from collections import Counter
import csv
import sys

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

def salvar_resultados_csv(contagem_autores, distribuicao_lotka, nome_arquivo):
    """
    Salva os resultados da Lei de Lotka em um arquivo CSV.

    Args:
        contagem_autores (dict): Contagem de artigos por autor.
        distribuicao_lotka (dict): Distribuição da Lei de Lotka.
        nome_arquivo (str): Nome do arquivo CSV.
    """
    with open(nome_arquivo, mode='w', newline='', encoding='utf-8') as file:
        writer = csv.writer(file)
        # Escrever cabeçalho para contagem de autores
        writer.writerow(["Autor", "Número de Artigos"])
        for autor, quantidade in contagem_autores.items():
            writer.writerow([autor, quantidade])

        # Linha em branco
        writer.writerow([])

        # Escrever cabeçalho para distribuição de Lotka
        writer.writerow(["Número de Artigos", "Número de Autores"])
        for num_artigos, num_autores in distribuicao_lotka.items():
            writer.writerow([num_artigos, num_autores])

if __name__ == "__main__":
    # Verificar se o número correto de argumentos foi fornecido
    if len(sys.argv) != 3:
        print("Uso: python3 lotka.py <arquivo_entrada> <arquivo_saida>")
        sys.exit(1)

    # Obter os arquivos de entrada e saída da linha de comando
    arquivo_entrada = sys.argv[1]
    arquivo_saida = sys.argv[2]

    # Ler o arquivo de entrada
    try:
        with open(arquivo_entrada, mode='r', encoding='utf-8') as file:
            lista_de_artigos = [linha.strip() for linha in file.readlines() if linha.strip()]
    except FileNotFoundError:
        print(f"Erro: O arquivo {arquivo_entrada} não foi encontrado.")
        sys.exit(1)

    # Calcular a Lei de Lotka
    contagem_autores, distribuicao_lotka = calcular_lei_de_lotka(lista_de_artigos)

    # Salvar os resultados em um arquivo CSV
    salvar_resultados_csv(contagem_autores, distribuicao_lotka, arquivo_saida)

    print(f"Resultados salvos em {arquivo_saida}")
