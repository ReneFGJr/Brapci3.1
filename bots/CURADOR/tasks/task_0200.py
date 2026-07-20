from rich.console import Console

console = Console()


def run(parametros=None, chat=None, silent=False):

    if parametros is None:
        parametros = []

    if not silent:
        console.print("[bold cyan]Revisão de palavras-chave[/bold cyan]")

    print(parametros)

    act = parametros[0] if parametros else ""

    print("====",act,"====")
    if act == "get":

    print(chat)

def get_keywords():
    # Implement the logic to retrieve keywords from the chat
    keywords = []  # Placeholder for actual keyword extraction logic
    return keywords
