import sys

from rich.align import Align
from rich.console import Console
from rich.panel import Panel
from rich.text import Text

from config import APP_NAME, APP_VERSION

console = Console()


def show():
    # Limpa toda a tela e fixa o início da renderização na linha 0, coluna 0.
    console.clear(home=True)
    sys.stdout.write("\033[H")
    sys.stdout.flush()

    largura = max(48, min(console.width, 84))

    titulo = Text(justify="center")
    titulo.append("\n")
    titulo.append("◆  ", style="bold bright_blue")
    titulo.append(APP_NAME, style="bold bright_cyan")
    titulo.append("  ◆\n", style="bold bright_blue")
    titulo.append(
        "Sistema Inteligente de Curadoria Científica\n",
        style="bright_white",
    )
    titulo.append("\n")
    titulo.append("VERSÃO  ", style="dim white")
    titulo.append(str(APP_VERSION), style="bold yellow")
    titulo.append("\n\n")
    titulo.append("Digite ", style="dim white")
    titulo.append("a", style="bold bright_green")
    titulo.append(" para abrir o menu de atalhos", style="dim white")

    console.print(
        Panel(
            Align.center(titulo, vertical="middle"),
            width=largura,
            border_style="bright_blue",
            padding=(1, 2),
            title="[bold bright_cyan] 🤖  ASSISTENTE IA [/bold bright_cyan]",
            title_align="center",
            subtitle="[dim bright_white] Rene Gabriel Junior [/dim bright_white]",
            subtitle_align="center",
        )
    )

    console.print("[dim]  Pronto para receber comandos.[/dim]\n")
