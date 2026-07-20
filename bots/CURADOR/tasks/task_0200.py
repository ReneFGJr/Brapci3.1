from rich.console import Console
import requests
from tasks import task_4002

console = Console()

URL_KEYWORDS = "https://cip.brapci.inf.br/api/brapci/keyword/get"


def run(parametros=None,chat=None,silent=False):

    parametros = parametros or []

    if not silent:
        console.print("[bold cyan]Revisão de palavras-chave[/bold cyan]")

    act = parametros[0] if parametros else ""

    if act == "get":
        keywords = get_keywords(silent=silent)

        if silent:
            return keywords

        console.print(keywords)


def get_keywords(silent=False):
    """
    Recupera as palavras-chave dos trabalhos da task_4002.
    """

    works = task_4002.carregar_ids()

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
