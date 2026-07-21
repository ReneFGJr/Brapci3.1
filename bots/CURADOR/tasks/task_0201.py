import os
from rich.console import Console
from google.cloud import translate_v2 as google_translate

console = Console()

credential_path = "../../.Google.json"
os.environ["GOOGLE_APPLICATION_CREDENTIALS"] = credential_path

translate_client = google_translate.Client()


def translate_text(text, target="en"):
    """
    Traduz um texto utilizando o Google Cloud Translate.
    """

    translation = translate_client.translate(
        text,
        target_language=target
    )

    return translation["translatedText"]


def run(parametros=None, chat=None, silent=False):

    parametros = parametros or []

    if len(parametros) < 2:
        console.print("[red]Uso: t <texto> [idioma][/red]")
        return

    texto = " ".join(parametros[1:])

    traducao = translate_text(texto)

    if silent:
        return {
            "text": texto,
            "translation": traducao
        }

    console.print("[bold cyan]Google Translate[/bold cyan]")
    console.print(f"[yellow]Original:[/yellow] {texto}")
    console.print(f"[green]Tradução:[/green] {traducao}")