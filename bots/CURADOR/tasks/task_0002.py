#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Task 0002: menu hierárquico de atalhos do CURADOR."""

import json
import os
from pathlib import Path
import sys
import time

from rich.console import Console
from rich.panel import Panel
from rich.table import Table


console = Console()
SHORTCUTS_PATH = Path(__file__).resolve().parent.parent / "data" / "shortcuts.json"

TASK = {
    "id": 2,
    "name": "Menu de atalhos",
    "description": "Exibe um menu hierárquico e monta comandos do CURADOR.",
    "patterns": ["a"],
    "parameters": [],
}


def carregar_shortcuts():
    with open(SHORTCUTS_PATH, encoding="utf-8") as arquivo:
        dados = json.load(arquivo)
    if not isinstance(dados, dict):
        raise ValueError("shortcuts.json deve conter um objeto JSON na raiz.")
    return dados


def filhos(item):
    valor = item.get("children", {}) if isinstance(item, dict) else {}
    return valor if isinstance(valor, dict) else {}


def localizar_opcao(opcoes, escolha):
    escolha_normalizada = escolha.casefold().strip()
    for nome, item in opcoes.items():
        aliases = item.get("aliases", []) if isinstance(item, dict) else []
        nomes = [nome, *aliases]
        if any(escolha_normalizada == str(valor).casefold() for valor in nomes):
            return nome, item
    return None, None


def validar_entrada(valor, item):
    if not valor:
        return False
    if item.get("validation") == "integer":
        return valor.isdigit()
    return True


def solicitar_entrada(nome, item):
    rotulo = item.get("prompt", nome)
    while True:
        valor = console.input(f"[bold cyan]{rotulo}:[/bold cyan] ").strip()
        if validar_entrada(valor, item):
            return valor
        console.print("[yellow]Valor inválido. Informe um número inteiro.[/yellow]")


def resolver_parametros(shortcuts, parametros):
    opcoes = shortcuts
    partes = []

    for parametro in parametros:
        nome, item = localizar_opcao(opcoes, str(parametro))
        if nome is None:
            entradas = [
                (nome_entrada, item_entrada)
                for nome_entrada, item_entrada in opcoes.items()
                if isinstance(item_entrada, dict) and item_entrada.get("input")
            ]
            if len(entradas) != 1:
                return None
            _, item_entrada = entradas[0]
            valor = str(parametro)
            if not validar_entrada(valor, item_entrada):
                return None
            partes.append(valor)
            opcoes = {}
            continue
        partes.append(nome)
        opcoes = filhos(item)

    if not partes or opcoes:
        return None
    return " ".join(partes)


def ler_tecla():
    """Lê setas, Enter e Escape sem exigir confirmação adicional."""
    if os.name == "nt":
        import msvcrt

        tecla = msvcrt.getwch()
        if tecla in ("\x00", "\xe0"):
            codigo = msvcrt.getwch()
            return {
                "H": "up",
                "P": "down",
                "K": "back",
                "M": "enter",
            }.get(codigo, "")
        if tecla == "\x1b":
            sequencia = ""
            limite = time.monotonic() + 0.30
            while time.monotonic() < limite and len(sequencia) < 2:
                if not msvcrt.kbhit():
                    time.sleep(0.005)
                    continue
                sequencia += msvcrt.getwch()
            return {
                "[A": "up",
                "[B": "down",
                "[D": "back",
                "[C": "enter",
                "OA": "up",
                "OB": "down",
                "OD": "back",
                "OC": "enter",
            }.get(sequencia, "back")
        if tecla == "\r":
            return "enter"
        return ""

    import termios
    import tty
    import select

    descritor = sys.stdin.fileno()
    configuracao = termios.tcgetattr(descritor)

    def ler_caractere():
        primeiro = os.read(descritor, 1)
        if not primeiro:
            return ""
        tamanho = 1
        valor = primeiro[0]
        if valor & 0xE0 == 0xC0:
            tamanho = 2
        elif valor & 0xF0 == 0xE0:
            tamanho = 3
        elif valor & 0xF8 == 0xF0:
            tamanho = 4
        restante = os.read(descritor, tamanho - 1) if tamanho > 1 else b""
        return (primeiro + restante).decode("utf-8", errors="replace")

    try:
        tty.setraw(descritor)
        tecla = ler_caractere()
        if tecla in ("\r", "\n"):
            return "enter"
        if tecla == "\x1b":
            if not select.select([descritor], [], [], 0.30)[0]:
                return "back"
            seguinte = ler_caractere()
            if seguinte not in ("[", "O"):
                return "back"
            codigo = ler_caractere()
            return {"A": "up", "B": "down", "D": "back", "C": "enter"}.get(codigo, "")
        return ""
    finally:
        termios.tcsetattr(descritor, termios.TCSADRAIN, configuracao)


def mostrar_opcoes(opcoes, caminho, selecionado):
    titulo = "Atalhos"
    if caminho:
        titulo += " › " + " › ".join(caminho)

    tabela = Table(show_header=False, box=None, padding=(0, 1))
    tabela.add_column("Cursor", style="bold cyan", justify="center")
    tabela.add_column("Opção", style="bold white")
    tabela.add_column("Tipo", style="dim")

    for indice, (nome, item) in enumerate(opcoes.items()):
        if item.get("input"):
            tipo = "digitar valor"
        else:
            tipo = "abrir ›" if filhos(item) else "comando"
        cursor = "❯" if indice == selecionado else " "
        estilo = "bold reverse" if indice == selecionado else None
        tabela.add_row(cursor, nome, tipo, style=estilo)

    console.print(Panel(tabela, title=titulo, border_style="blue"))
    console.print("[dim]↑/↓ mover  •  Enter selecionar  •  Esc/← voltar[/dim]")


def menu_interativo(shortcuts):
    opcoes = shortcuts
    caminho = []
    pilha = []
    selecionado = 0

    while True:
        console.clear()
        mostrar_opcoes(opcoes, caminho, selecionado)
        tecla = ler_tecla()

        if tecla == "up":
            selecionado = (selecionado - 1) % len(opcoes)
            continue

        if tecla == "down":
            selecionado = (selecionado + 1) % len(opcoes)
            continue

        if tecla == "back":
            if not pilha:
                return None
            opcoes, caminho, selecionado = pilha.pop()
            continue

        if tecla != "enter":
            continue

        nome, item = list(opcoes.items())[selecionado]
        if item.get("input"):
            valor = solicitar_entrada(nome, item)
            return " ".join([*caminho, valor])

        subopcoes = filhos(item)
        if subopcoes:
            pilha.append((opcoes, caminho.copy(), selecionado))
            caminho.append(nome)
            opcoes = subopcoes
            selecionado = 0
            continue

        return " ".join([*caminho, nome])


def run(parametros=None, chat=None, silent=False):
    parametros = parametros or []

    try:
        shortcuts = carregar_shortcuts()
    except Exception as erro:
        return {"success": False, "error": f"Erro ao carregar atalhos: {erro}"}

    if parametros:
        comando = resolver_parametros(shortcuts, parametros)
        if comando is None:
            return {"success": False, "error": "Atalho não encontrado ou incompleto."}
    elif silent:
        return {"success": True, "items": list(shortcuts.keys())}
    else:
        comando = menu_interativo(shortcuts)

    if comando is None:
        if silent:
            return {"success": False, "cancelled": True}
        return None

    if not silent:
        console.print(f"\n[green]Comando montado:[/green] [bold white]{comando}[/bold white]\n")
        return None

    return {"success": True, "command": comando}


if __name__ == "__main__":
    run()
