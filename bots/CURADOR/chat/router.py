import json

with open("data/intents.json", encoding="utf8") as f:
    INTENTS = json.load(f)


def localizar(texto):

    texto = texto.lower().strip()

    if not texto:
        return None

    comando = texto.split()[0]

    for codigo, intent in INTENTS.items():

        for pattern in intent["patterns"]:

            if comando == pattern.lower():

                return int(codigo)

    return None