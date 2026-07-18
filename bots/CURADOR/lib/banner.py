from rich.console import Console
from rich.panel import Panel
from rich.text import Text

from config import APP_NAME, APP_VERSION

console = Console()


def show():

    titulo = Text()
    titulo.append(f"{APP_NAME}\n", style="bold bright_cyan")
    titulo.append(
        "Sistema Inteligente de Curadoria Científica\n",
        style="white"
    )
    titulo.append(
        f"Versão {APP_VERSION}",
        style="bold yellow"
    )

    console.print()

    console.print(
        Panel.fit(
            titulo,
            border_style="bright_blue",
            padding=(1, 6),
            title="🤖 Assistente IA",
            subtitle="Rene Gabriel Junior"
        )
    )

    console.print()