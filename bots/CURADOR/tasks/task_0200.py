from rich.console import Console

console = Console()

def run(parametros=None, chat=None):

    if parametros is None:
        parametros = []

    console.print("[bold cyan]Revisão de palavras-chave[/bold cyan]")

    print(parametros)
    print(chat)