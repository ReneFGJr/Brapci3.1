import json
from pathlib import Path

DATA_PATH = Path(__file__).resolve().parent.parent / "data" / "intents.json"


def carregar_intents():
    try:
        with open(DATA_PATH, encoding="utf8") as f:
            return json.load(f)
    except Exception:
        with open("data/intents.json", encoding="utf8") as f:
            return json.load(f)


INTENTS = carregar_intents()


def localizar(texto):

    texto = texto.lower().strip()

    if not texto:
        return None

    if texto.isdigit():
        return int(texto)

    intents = carregar_intents()

    # 1. Correspondência exata com o texto completo ou prefixo com parâmetros
    for codigo, intent in intents.items():
        for pattern in intent.get("patterns", []):
            p = pattern.lower().strip()
            if texto == p or texto.startswith(p + " "):
                return int(codigo.strip())

    # 2. Correspondência pela primeira palavra
    comando = texto.split()[0]

    for codigo, intent in intents.items():
        for pattern in intent.get("patterns", []):
            p = pattern.lower().strip()
            if comando == p:
                return int(codigo.strip())

    return None