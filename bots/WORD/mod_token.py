import spacy

# Baixe o modelo de linguagem português
#!python -m spacy download pt_core_news_sm

file = 'txt/sample.txt'
with open(file, 'r', encoding='utf-8') as file:
    text = file.read()

nlp = spacy.load("pt_core_news_sm")
doc = nlp(text)

tokens = [token.text for token in doc]

for item in tokens:
    print(item)
