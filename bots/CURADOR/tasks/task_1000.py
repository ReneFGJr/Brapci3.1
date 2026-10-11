# tasks/task_1000.py

from rich.console import Console
from rich.panel import Panel


console = Console()

TASK = {
    "id": 1000,
    "name": "Coleta de Revistas",
    "description": "Realiza a coleta de periódicos cadastrados na BRAPCI.",
    "patterns": [
        "coletar revistas",
        "iniciar coleta",
        "atualizar periódicos",
        "buscar novas revistas",
        "rodar harvesting"
    ],
    "parameters": [
        {
            "name": "fonte",
            "type": "string",
            "required": False
        }
    ]
}


def run(parametros=None,chat=None,silent=False):

    console.print()
    console.print(
        Panel.fit(
            "[bold bright_cyan]⏳ Iniciando coleta...[/bold bright_cyan]\n"
            "[dim white]Preparando o harvesting das revistas da BRAPCI.[/dim white]",
            title="[bold blue]CURADOR[/bold blue]",
            border_style="bright_blue",
            padding=(0, 2),
        )
    )
    console.print()

    # coleta
