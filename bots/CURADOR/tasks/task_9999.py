# tasks/task_1000.py
from pathlib import Path
import json

from rich.console import Console
from rich.table import Table

console = Console()

TASK = {
    "id": 9999,
    "name": "Ajusta",
    "description": "Mostra a ajuda do CURADOR.",
    "patterns": [
        "help",
        "ajuda",
        "como usar",
        "ajuda curador"
    ],
    "parameters": [
        {
            "name": "fonte",
            "type": "string",
            "required": False
        }
    ]
}

def ajuda():

    arquivo = Path("data/intents.json")

    if not arquivo.exists():

        console.print(
            "[bold red]Arquivo data/intents.json não encontrado.[/bold red]"
        )
        return False

    try:

        with open(
            arquivo,
            "r",
            encoding="utf-8"
        ) as f:

            intents = json.load(f)

    except Exception as e:

        console.print(
            f"[bold red]Erro ao ler intents.json:[/bold red] {e}"
        )
        return False

    table = Table(
        title="CURADOR - Comandos Disponíveis",
        header_style="bold cyan",
        show_lines=True
    )

    table.add_column("Código", style="yellow", width=8)
    table.add_column("Descrição", style="green", width=35)
    table.add_column("Exemplos", style="white")

    for codigo in sorted(intents.keys()):

        intent = intents[codigo]

        descricao = intent.get("description", "")

        exemplos = ", ".join(intent.get("patterns", [])[:3])

        table.add_row(
            codigo.strip(),
            descricao,
            exemplos
        )

    console.print()
    console.print(table)
    console.print()

    console.print("[bold cyan]Exemplo:[/bold cyan]")
    console.print("  CURADOR > atualizar")
    console.print("  CURADOR > coletar revistas")
    console.print("  CURADOR > gerar palavras-chave")
    console.print()

    return True


def run(args):

    ajuda()
    return True

    # coleta