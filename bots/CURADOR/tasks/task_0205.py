#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Task 0205: verifica problemas de codificação em rdf_literal.n_name."""

import os
import re
import sys
from pathlib import Path

import pymysql
from dotenv import load_dotenv
from rich import box
from rich.console import Console
from rich.panel import Panel
from rich.table import Table


console = Console()
BASE_DIR = Path(__file__).resolve().parent.parent
load_dotenv(BASE_DIR / ".env")
LIMITE = 50

TASK = {
    "id": 205,
    "name": "Verificar UTF-8 dos literais",
    "description": "Localiza problemas de caracteres em brapci_rdf.rdf_literal.",
    "patterns": ["literal check", "check literal", "verificar literal"],
    "parameters": [],
}


def get_connection():
    return pymysql.connect(
        host=os.getenv("DB_HOST", "localhost"),
        port=int(os.getenv("DB_PORT", 3306)),
        user=os.getenv("DB_USERNAME", "root"),
        password=os.getenv("DB_PASSWORD", ""),
        database="brapci_rdf",
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=True,
    )


def diagnosticar(hexadecimal):
    if not hexadecimal:
        return None, ""

    bruto = bytes.fromhex(hexadecimal)
    try:
        texto = bruto.decode("utf-8", errors="strict")
    except UnicodeDecodeError as erro:
        texto = bruto.decode("utf-8", errors="replace")
        return f"UTF-8 inválido no byte {erro.start}", texto

    if "\ufffd" in texto:
        return "Caractere de substituição U+FFFD", texto

    controles = [
        caractere
        for caractere in texto
        if ord(caractere) < 32 and caractere not in "\t\n\r"
    ]
    if controles:
        codigos = ", ".join(f"U+{ord(c):04X}" for c in sorted(set(controles)))
        return f"Caractere de controle: {codigos}", texto

    # Sequências comuns de texto UTF-8 interpretado anteriormente como Latin-1/Windows-1252.
    if re.search(r"(?:Ã.|Â.|â.|ðŸ)", texto):
        return "Possível texto corrompido (mojibake)", texto

    return None, texto


def localizar_problemas(limite=LIMITE):
    conexao = get_connection()
    problemas = []
    examinados = 0
    ultimo_id = 0

    try:
        with conexao.cursor() as cursor:
            # HEX evita que bytes inválidos sejam decodificados pelo driver antes da análise.
            while len(problemas) < limite:
                cursor.execute(
                    """
                    SELECT id_n, n_lang, HEX(CAST(n_name AS BINARY)) AS n_hex
                    FROM brapci_rdf.rdf_literal
                    WHERE id_n > %s
                    ORDER BY id_n
                    LIMIT 5000
                    """,
                    (ultimo_id,),
                )
                registros = cursor.fetchall() or []
                if not registros:
                    break

                for registro in registros:
                    ultimo_id = int(registro["id_n"])
                    examinados += 1
                    problema, texto = diagnosticar(registro.get("n_hex"))
                    if problema:
                        problemas.append(
                            {
                                "id_n": ultimo_id,
                                "n_lang": registro.get("n_lang") or "-",
                                "problema": problema,
                                "texto": texto,
                            }
                        )
                        if len(problemas) >= limite:
                            break
    finally:
        conexao.close()

    return problemas, examinados


def trecho(texto, tamanho=52):
    texto = str(texto).replace("\r", "\\r").replace("\n", "\\n").replace("\t", "\\t")
    return texto if len(texto) <= tamanho else texto[: tamanho - 3] + "..."


def exibir(problemas, examinados):
    console.print()
    console.print(
        Panel(
            "[bold bright_cyan]brapci_rdf.rdf_literal[/bold bright_cyan]\n"
            "[dim]Verificação de codificação e caracteres suspeitos em n_name[/dim]",
            title="[bold blue]TASK 0205 · UTF-8 CHECK[/bold blue]",
            border_style="bright_blue",
            box=box.DOUBLE,
            width=min(console.width, 79),
        )
    )

    if not problemas:
        console.print(
            f"[bold green]✓ Nenhum problema encontrado.[/bold green] "
            f"[dim]Registros examinados: {examinados:,}[/dim]".replace(",", ".")
        )
        console.print()
        return

    tabela = Table(box=box.ROUNDED, border_style="red", show_lines=True)
    tabela.add_column("ID", justify="right", style="cyan", no_wrap=True)
    tabela.add_column("Idioma", style="yellow", no_wrap=True)
    tabela.add_column("Problema", style="bold red", width=25)
    tabela.add_column("Conteúdo", style="white", overflow="fold")

    for item in problemas:
        tabela.add_row(
            str(item["id_n"]),
            str(item["n_lang"]),
            item["problema"],
            trecho(item["texto"]),
        )

    console.print(tabela)
    console.print(
        f"[yellow]Encontrados {len(problemas)} problema(s); exibição limitada a {LIMITE}.[/yellow] "
        f"[dim]Registros examinados: {examinados:,}[/dim]".replace(",", ".")
    )
    console.print()


def run(parametros=None, chat=None, silent=False):
    try:
        problemas, examinados = localizar_problemas()
    except Exception as erro:
        if not silent:
            console.print(f"[bold red]Erro ao verificar rdf_literal:[/bold red] {erro}")
            return None
        return {"success": False, "error": str(erro)}

    resultado = {
        "success": True,
        "limite": LIMITE,
        "examinados": examinados,
        "total_exibido": len(problemas),
        "problemas": problemas,
    }

    if silent:
        return resultado

    exibir(problemas, examinados)
    return None


if __name__ == "__main__":
    run(parametros=sys.argv[1:])
