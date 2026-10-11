#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Task 0002: menu hierárquico de atalhos do CURADOR."""

import json
from pathlib import Path

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


def mostrar_opcoes(opcoes, caminho):
    titulo = "Atalhos"
    if caminho:
        titulo += " › " + " › ".join(caminho)

    tabela = Table(show_header=False, box=None, padding=(0, 1))
    tabela.add_column("Número", style="bold cyan", justify="right")
    tabela.add_column("Opção", style="bold white")
    tabela.add_column("Tipo", style="dim")

    for indice, (nome, item) in enumerate(opcoes.items(), 1):
        tipo = "abrir ›" if filhos(item) else "comando"
        tabela.add_row(str(indice), nome, tipo)

    if caminho:
        tabela.add_row("0", "voltar", "")
    tabela.add_row("q", "cancelar", "")
    console.print(Panel(tabela, title=titulo, border_style="blue"))


def menu_interativo(shortcuts):
    opcoes = shortcuts
    caminho = []
    pilha = []

    while True:
        mostrar_opcoes(opcoes, caminho)
        escolha = input("Selecione uma opção: ").strip()

        if escolha.casefold() in ("q", "sair", "cancelar"):
            return None

        if escolha == "0" and pilha:
            opcoes, caminho = pilha.pop()
            continue

        itens = list(opcoes.items())
        if escolha.isdigit() and 1 <= int(escolha) <= len(itens):
            nome, item = itens[int(escolha) - 1]
        else:
            nome, item = localizar_opcao(opcoes, escolha)

        if nome is None:
            console.print("[yellow]Opção inválida.[/yellow]\n")
            continue

        subopcoes = filhos(item)
        if subopcoes:
            pilha.append((opcoes, caminho.copy()))
            caminho.append(nome)
            opcoes = subopcoes
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
