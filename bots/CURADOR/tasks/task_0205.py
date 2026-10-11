#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Task 0205: verifica problemas de codificação em rdf_literal.n_name."""

import os
import re
import sys
import unicodedata
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


def get_connection(autocommit=True):
    return pymysql.connect(
        host=os.getenv("DB_HOST", "localhost"),
        port=int(os.getenv("DB_PORT", 3306)),
        user=os.getenv("DB_USERNAME", "root"),
        password=os.getenv("DB_PASSWORD", ""),
        database="brapci_rdf",
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=autocommit,
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

    # Sequências específicas de UTF-8 interpretado como Latin-1/Windows-1252.
    # Não sinaliza letras legítimas como â em "Rosângela" ou Â em "Ângela".
    padrao_mojibake = (
        r"(?:"
        r"Ã[\x80-\xBF]|"
        r"Â[\x80-\xBF]|"
        r"â[€‚ƒ„…†‡ˆ‰Š‹ŒŽ‘’“”•–—˜™š›œžŸ]|"
        r"ðŸ"
        r")"
    )
    if re.search(padrao_mojibake, texto):
        return "Possível texto corrompido (mojibake)", texto

    return None, texto


def pontuar_corrupcao(texto):
    """Quanto menor a pontuação, menor a evidência de texto corrompido."""
    padroes = (
        r"Ã[\x80-\xBF]",
        r"Â[\x80-\xBF]",
        r"â[€‚ƒ„…†‡ˆ‰Š‹ŒŽ‘’“”•–—˜™š›œžŸ]",
        r"ðŸ",
        r"[Ìì][\x80-\xBF]",
    )
    total = sum(len(re.findall(padrao, texto)) for padrao in padroes)
    total += texto.count("\ufffd") * 5
    total += sum(1 for caractere in texto if ord(caractere) < 32 and caractere not in "\t\n\r") * 3
    return total


def corrigir_automaticamente(texto):
    """Gera uma sugestão sem alterar textos cuja conversão não seja claramente melhor."""
    candidatos = [texto]

    for codificacao in ("latin-1", "cp1252"):
        try:
            candidatos.append(texto.encode(codificacao).decode("utf-8", errors="strict"))
        except (UnicodeEncodeError, UnicodeDecodeError):
            pass

    # Corrige marcas de acento combinante vistas em bases convertidas mais de uma vez.
    substituicoes = {
        "Â¿": "¿",
        "Â¡": "¡",
        "Ì\x80": "\u0300",
        "Ì\x81": "\u0301",
        "Ì\x82": "\u0302",
        "Ì\x83": "\u0303",
        "ì\x80": "\u0300",
        "ì\x81": "\u0301",
        "ì\x82": "\u0302",
        "ì\x83": "\u0303",
    }
    candidato_manual = texto
    for incorreto, correto in substituicoes.items():
        candidato_manual = candidato_manual.replace(incorreto, correto)
    candidatos.append(candidato_manual)

    candidatos = [unicodedata.normalize("NFC", candidato) for candidato in candidatos]
    original_normalizado = unicodedata.normalize("NFC", texto)
    melhor = min(candidatos, key=pontuar_corrupcao)

    if melhor != original_normalizado and pontuar_corrupcao(melhor) < pontuar_corrupcao(original_normalizado):
        return melhor
    return None


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
                    correcao = corrigir_automaticamente(texto)
                    problemas.append(
                            {
                                "id_n": ultimo_id,
                                "n_lang": registro.get("n_lang") or "-",
                            "problema": problema,
                            "texto": texto,
                            "correcao": correcao,
                            }
                        )
                        if len(problemas) >= limite:
                            break
    finally:
        conexao.close()

    return problemas, examinados


def aplicar_correcoes(problemas):
    correcoes = [item for item in problemas if item.get("correcao")]
    if not correcoes:
        return 0

    conexao = get_connection(autocommit=False)
    try:
        with conexao.cursor() as cursor:
            total = 0
            for item in correcoes:
                cursor.execute(
                    """
                    UPDATE brapci_rdf.rdf_literal
                    SET n_name = %s
                    WHERE id_n = %s AND n_name = %s
                    """,
                    (item["correcao"], item["id_n"], item["texto"]),
                )
                total += cursor.rowcount
        conexao.commit()
        return total
    except Exception:
        conexao.rollback()
        raise
    finally:
        conexao.close()


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
    tabela.add_column("Correção sugerida", style="green", overflow="fold")

    for item in problemas:
        tabela.add_row(
            str(item["id_n"]),
            str(item["n_lang"]),
            item["problema"],
            trecho(item["texto"]),
            trecho(item["correcao"]) if item.get("correcao") else "[dim]sem correção segura[/dim]",
        )

    console.print(tabela)
    console.print(
        f"[yellow]Encontrados {len(problemas)} problema(s); exibição limitada a {LIMITE}.[/yellow] "
        f"[dim]Registros examinados: {examinados:,}[/dim]".replace(",", ".")
    )
    console.print()


def run(parametros=None, chat=None, silent=False):
    parametros = parametros or []
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
        if any(str(p).lower() in ("-y", "--yes", "corrigir", "fix") for p in parametros):
            resultado["total_corrigido"] = aplicar_correcoes(problemas)
        return resultado

    exibir(problemas, examinados)

    corrigiveis = [item for item in problemas if item.get("correcao")]
    if corrigiveis:
        resposta = console.input(
            f"[bold yellow]Aplicar {len(corrigiveis)} correção(ões) sugerida(s)? [s/N]:[/bold yellow] "
        ).strip().casefold()
        if resposta in ("s", "sim", "y", "yes"):
            try:
                total = aplicar_correcoes(corrigiveis)
                console.print(f"[bold green]✓ {total} registro(s) corrigido(s) com sucesso.[/bold green]\n")
            except Exception as erro:
                console.print(f"[bold red]Erro ao aplicar correções; transação revertida:[/bold red] {erro}\n")
        else:
            console.print("[dim]Nenhuma alteração foi gravada.[/dim]\n")
    return None


if __name__ == "__main__":
    run(parametros=sys.argv[1:])
