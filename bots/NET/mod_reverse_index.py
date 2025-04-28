import unicodedata
import database
import sys

def index(word: str, doc_id: int):
    wd = split_string(word)
    for w in wd:
        ida = register_word(w)
        if (ida > 0):
            register_word_doc(ida, doc_id)

def register_word_doc(word: str, doc_id: int):
    qr = f"SELECT id_ad  FROM brapci_elastic.ri_authors_docs WHERE ad_author  = {word} and ad_doc = {doc_id}"
    row = database.query(qr)
    if not row:
        # Se a palavra não existe, insere uma nova entrada
        qi = f"INSERT INTO brapci_elastic.ri_authors_docs (ad_author, ad_doc) VALUES ('{word}','{doc_id}')"
        database.insert(qi)
    return 1

def split_string(string: str) -> list[str]:
    # 1) Normaliza a string para decompor caracteres acentuados
    normalized = unicodedata.normalize('NFKD', string)
    # 2) Remove todos os diacríticos (marcas de acento)
    without_accents = ''.join(
        c for c in normalized
        if not unicodedata.combining(c)
    )
    # 3) Converte para caixa baixa
    lowercased = without_accents.lower()
    # 4) Divide em palavras usando espaços como delimitador
    palavras = lowercased.split()
    return palavras

def register_word(word: str):
    print(f"Word1: {word}")
    word = word[0:200]
    print(f"Word2: {word}")

    if word.length() < 5:
        return 0
    # Registra a palavra no índice invertido
    qr = f"SELECT id_w FROM brapci_elastic.ri_words WHERE w_name = '{word}'"
    row = database.query(qr)
    if not row:
        # Se a palavra não existe, insere uma nova entrada
        qi = f"INSERT INTO brapci_elastic.ri_words (w_name) VALUES ('{word}')"
        database.insert(qi)
        row = database.query(qr)
    return row[0][0]
if __name__ == "__main__":
    # Exemplo de uso
    print("Reverse Index")
    print("===============================================")
    wd = split_string("Renê Santos de Oliveira")
    #Registra a palavra no índice invertido
    for word in wd:
        register_word(word)
