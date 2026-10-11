#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Task 0002: menu hierárquico de atalhos do CURADOR."""

import json
import os
from pathlib import Path
import sys

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


def resolver_parametros(shortcuts, parametros):
    opcoes = shortcuts
    partes = []

    for parametro in parametros:
        nome, item = localizar_opcao(opcoes, str(parametro))
        if nome is None:
            return None
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
        if tecla == "\r":
            return "enter"
        if tecla == "\x1b":
            return "back"
        return ""

    import termios
    import tty

    descritor = sys.stdin.fileno()
    configuracao = termios.tcgetattr(descritor)
    try:
        tty.setraw(descritor)
        tecla = sys.stdin.read(1)
        if tecla in ("\r", "\n"):
            return "enter"
        if tecla == "\x1b":
            seguinte = sys.stdin.read(1)
            if seguinte != "[":
                return "back"
            codigo = sys.stdin.read(1)
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
        return {"success": False, "cancelled": True}

    if not silent:
        console.print(f"\n[green]Comando montado:[/green] [bold white]{comando}[/bold white]\n")
        return None

    return {"success": True, "command": comando}


if __name__ == "__main__":
    run()
