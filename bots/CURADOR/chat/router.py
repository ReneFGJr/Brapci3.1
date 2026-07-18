import json

with open("data/intents.json", encoding="utf8") as f:
    INTENTS = json.load(f)


def localizar(texto):

    texto = texto.lower()

    for codigo, intent in INTENTS.items():

        for pattern in intent["patterns"]:

            if pattern.lower() in texto:

                return int(codigo)

    return None