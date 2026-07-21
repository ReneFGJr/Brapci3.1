import requests
import re, os
from tasks import task_4002
from rich.console import Console
from rich.table import Table
from rich.text import Text

console = Console()

URL_KEYWORDS = "https://cip.brapci.inf.br/api/brapci/keyword/get"
URL_KEYWORD_FIX = "https://cip.brapci.inf.br/api/brapci/keyword/fix"

def run(parametros=None, chat=None, silent=False):

    parametros = parametros or []
    act = parametros[0].lower() if parametros else "get"

    if act == "fix":
        if len(parametros) < 3:
            console.print("[red]Uso: fix <ID> <IDfix>[/red]")
        else:
            keyword_fix(parametros[1], parametros[2], silent=silent)
        return []

    if not silent:
        console.print(f"[bold cyan]Revisão de palavras-chave: {act}[/bold cyan]")

    # Recupera os dados apenas uma vez
    keywords = get_keywords(silent=silent)

    if silent:
        return keywords

    if act == "get":
        show_keywords(keywords)

    elif act in ("errors", "erros"):
        console.print("[bold yellow]Exibindo apenas palavras-chave com possíveis problemas de codificação...[/bold yellow]")
        show_keyword_errors(keywords)

    else:
        console.print(f"[bold red]Ação desconhecida:[/bold red] {act}")


def get_keywords(silent=False):
    """
    Recupera as palavras-chave dos trabalhos da task_4002.
    """

    works = task_4002.carregar_ids()
    console.print(f"[green]Carregar IDs API: {len(works)} ...[/green]")

    if not works:
        return []

    payload = {"idz": works}

    try:
        response = requests.post(
            URL_KEYWORDS,
            data=payload,  # use json=payload se sua API receber JSON
            timeout=60)

        response.raise_for_status()
        return response.json()

    except requests.exceptions.RequestException as e:
        if not silent:
            console.print(f"[red]Erro ao acessar API:[/red] {e}")
        return []

    except ValueError:
        if not silent:
            console.print("[red]A API não retornou um JSON válido.[/red]")
        return []

import re

def has_encoding_problem(text):
    patterns = [
        r"\?{2,}",      # ?????
        r"�",           # caractere inválido
        r"Ã.",          # Ã§ Ã£ Ã¡ ...
        r"Â.",          # Â°
        r"ã³",          # mojibake
        r"ã§",
        r"ã¡",
        r"ãª",
        r"ãµ",
        r"çõ",          # Indexaçõo
        r"õo",          # Avaliaçõo
    ]

    return any(re.search(p, text) for p in patterns)

def show_keywords(data):

    table = Table(
        title="Palavras-chave",
        show_lines=True,
        header_style="bold white on blue"
    )

    table.add_column("⚠", justify="center", width=3)
    table.add_column("ID", style="cyan", justify="right")
    table.add_column("Idioma", justify="center")
    table.add_column("Ocorr.", justify="right", style="green")
    table.add_column("Palavra-chave", style="white")

    cores = {
        "pt": "green",
        "en": "cyan",
        "es": "yellow"
    }

    for item in sorted(data["data"], key=lambda x: (x["lang"], x["name"].lower())):

        alerta = ""

        if has_encoding_problem(item["name"]):
            alerta = "[bold red]⚠[/bold red]"

        idioma = Text(
            item["lang"].upper(),
            style=f"bold {cores.get(item['lang'], 'white')}"
        )

        table.add_row(
            alerta,
            str(item["ID"]),
            idioma,
            str(item["count"]),
            item["name"]
        )

    console.print(table)

from rich.table import Table
from rich.text import Text


def show_keyword_errors(data):
    """
    Exibe somente as palavras-chave com possíveis problemas de codificação.
    """

    erros = [
        item
        for item in data.get("data", [])
        if has_encoding_problem(item["name"])
    ]

    if not erros:
        console.print("[bold green]✓ Nenhum problema de codificação encontrado.[/bold green]")
        return

    table = Table(
        title=f"Palavras-chave com problemas ({len(erros)})",
        show_lines=True,
        header_style="bold white on red"
    )

    table.add_column("ID", style="cyan", justify="right")
    table.add_column("Idioma", justify="center")
    table.add_column("Ocorr.", justify="right", style="green")
    table.add_column("Palavra-chave", style="bold red")

    cores = {
        "pt": "green",
        "en": "cyan",
        "es": "yellow"
    }

    for item in sorted(erros, key=lambda x: (x["lang"], x["name"].lower())):

        idioma = Text(
            item["lang"].upper(),
            style=f"bold {cores.get(item['lang'], 'white')}"
        )

        table.add_row(
            str(item["ID"]),
            idioma,
            str(item["count"]),
            item["name"]
        )

    console.print(table)

def keyword_fix(ID, IDfix, silent=False):
    """
    Corrige uma palavra-chave, substituindo ID por IDfix.
    """

    api_key = os.getenv("USER_API")

    if not api_key:
        msg = {
            "status": 500,
            "status_message": "Variável USER_API não encontrada no arquivo .env"
        }

        if silent:
            return msg

        console.print(f"[bold red]{msg['status_message']}[/bold red]")
        return

    payload = {
        "apikey": api_key,
        "idz": ID,
        "idfix": IDfix
    }

    try:
        response = requests.post(
            URL_KEYWORD_FIX,
            data=payload,
            timeout=60
        )

        response.raise_for_status()

        result = response.json()

        if silent:
            return result

        console.print_json(data=result)

        return result

    except requests.RequestException as e:
        msg = {
            "status": 500,
            "status_message": str(e)
        }

        if silent:
            return msg

        console.print_json(data=msg)

        return msg