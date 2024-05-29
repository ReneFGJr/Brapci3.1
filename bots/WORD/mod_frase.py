import nltk

def process(text):

    nltk.download('punkt')
    from nltk.tokenize import sent_tokenize

    # Tokenizar o texto em frases
    sentences = sent_tokenize(text)

    # Imprimir as frases separadas
    for i, sentence in enumerate(sentences):
        sentence = sentence.replace(chr(13),' ')
        sentence = sentence.replace(chr(10),' ')
        print(f"Frase {i+1}: {sentence}")
