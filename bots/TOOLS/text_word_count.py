import re
import sys
import csv
from collections import Counter
from itertools import zip_longest


def contar_palavras(arquivo_entrada, arquivo_saida):
    """Conta termos de 1 a 5 palavras e grava em CSV."""
    with open(arquivo_entrada, "r", encoding="utf-8") as f:
        texto = f.read().lower()

    # Mantem apenas tokens de palavras para evitar pontuacao na contagem.
    palavras = re.findall(r"[a-zA-ZÀ-ÖØ-öø-ÿ0-9]+", texto)

    def gerar_contagem_ngrama(tokens, n, minimo_frequencia=1):
        if len(tokens) < n:
            return []

        ngramas = [" ".join(tokens[i : i + n]) for i in range(len(tokens) - n + 1)]
        frequencias = Counter(ngramas)
        filtradas = [item for item in frequencias.items() if item[1] >= minimo_frequencia]

        # Ordena da maior para a menor frequencia; em empate, ordem alfabetica.
        return sorted(filtradas, key=lambda item: (-item[1], item[0]))

    unigrama = gerar_contagem_ngrama(palavras, 1, minimo_frequencia=1)
    bigrama = gerar_contagem_ngrama(palavras, 2, minimo_frequencia=2)
    trigrama = gerar_contagem_ngrama(palavras, 3, minimo_frequencia=2)
    quadrigrama = gerar_contagem_ngrama(palavras, 4, minimo_frequencia=2)
    pentagrama = gerar_contagem_ngrama(palavras, 5, minimo_frequencia=2)

    cabecalho = [
        "termo_1",
        "freq_1",
        "termo_2",
        "freq_2",
        "termo_3",
        "freq_3",
        "termo_4",
        "freq_4",
        "termo_5",
        "freq_5",
    ]

    with open(arquivo_saida, "w", encoding="utf-8", newline="") as f:
        writer = csv.writer(f)
        writer.writerow(cabecalho)

        for linha in zip_longest(unigrama, bigrama, trigrama, quadrigrama, pentagrama, fillvalue=("", "")):
            writer.writerow(
                [
                    linha[0][0], linha[0][1],
                    linha[1][0], linha[1][1],
                    linha[2][0], linha[2][1],
                    linha[3][0], linha[3][1],
                    linha[4][0], linha[4][1],
                ]
            )


if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: python3 text_word_count.py <input_file>")
        sys.exit(1)

    input_file = sys.argv[1]
    contar_palavras(input_file, input_file.replace('.txt', '.csv'))