#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Task 0206: remove registros repetidos de brapci_rdf.rdf_data."""

import os
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

TASK = {
    "id": 206,
    "name": "Remover duplicatas de rdf_data",
    "description": (
        "Remove registros repetidos de brapci_rdf.rdf_data, considerando "
        "d_r1, d_p, d_r2 e d_literal, preservando o menor id_d."
    ),
    "patterns": [
        "dataset remove rdf_duplicates",
        "remove rdf_duplicates",
        "rdf duplicates",
    ],
    "parameters": [],
}


def get_connection(autocommit=False):
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


def localizar_grupos(cursor):
    cursor.execute(
        """
        SELECT
            d_r1,
            d_p,
            d_r2,
            d_literal,
            MIN(id_d) AS id_preservado,
            COUNT(*) AS total
        FROM brapci_rdf.rdf_data
        GROUP BY d_r1, d_p, d_r2, d_literal
        HAVING COUNT(*) > 1
        ORDER BY MIN(id_d)
        """
    )
    return cursor.fetchall() or []


def ids_do_grupo(cursor, grupo):
    cursor.execute(
        """
        SELECT id_d
        FROM brapci_rdf.rdf_data
        WHERE d_r1 <=> %s
          AND d_p <=> %s
          AND d_r2 <=> %s
          AND d_literal <=> %s
        ORDER BY id_d
        """,
        (grupo["d_r1"], grupo["d_p"], grupo["d_r2"], grupo["d_literal"]),
    )
    return [int(item["id_d"]) for item in (cursor.fetchall() or [])]


def excluir_ids(cursor, ids):
    if not ids:
        return 0
    marcadores = ", ".join(["%s"] * len(ids))
    cursor.execute(
        f"DELETE FROM brapci_rdf.rdf_data WHERE id_d IN ({marcadores})",
        tuple(ids),
    )
    return cursor.rowcount


def valor(valor):
    return "NULL" if valor is None else str(valor)


def mostrar_cabecalho(total_grupos, total_repetidos, simular):
    modo = "[bold yellow]SIMULAÇÃO[/bold yellow]" if simular else "[bold red]EXCLUSÃO REAL[/bold red]"
    console.print()
    console.print(
        Panel(
            "[bold bright_cyan]brapci_rdf.rdf_data[/bold bright_cyan]\n"
            "Duplicidade por [white]d_r1 + d_p + d_r2 + d_literal[/white]\n"
            f"Modo: {modo}",
            title="[bold blue]TASK 0206 · REMOVER DUPLICATAS[/bold blue]",
            subtitle="[green]menor id_d preservado[/green]",
            border_style="bright_blue",
            box=box.DOUBLE,
            width=min(console.width, 100),
        )
    )
    console.print(
        f"[white]Grupos duplicados:[/white] [bold cyan]{total_grupos:,}[/bold cyan]  "
        f"[white]Registros a remover:[/white] [bold yellow]{total_repetidos:,}[/bold yellow]".replace(",", ".")
    )


def executar_remocao(parametros=None, silent=False):
    parametros = [str(item).casefold() for item in (parametros or [])]
    simular = any(item in ("simular", "simulação", "dry-run", "--dry-run") for item in parametros)
    confirmar_automaticamente = any(item in ("-y", "--yes") for item in parametros)
    conexao = get_connection(autocommit=False)

    try:
        with conexao.cursor() as cursor:
            grupos = localizar_grupos(cursor)
            total_repetidos = sum(int(grupo["total"]) - 1 for grupo in grupos)

            if not silent:
                mostrar_cabecalho(len(grupos), total_repetidos, simular)

            if not grupos:
                if not silent:
                    console.print("[bold green]✓ Nenhum registro duplicado encontrado.[/bold green]\n")
                conexao.rollback()
                return {
                    "success": True,
                    "simulacao": simular,
                    "grupos": 0,
                    "removidos": 0,
                    "detalhes": [],
                }

            if silent and not simular and not confirmar_automaticamente:
                conexao.rollback()
                return {
                    "success": False,
                    "confirmation_required": True,
                    "error": "Use --yes para confirmar a exclusão ou --dry-run para simular.",
                    "grupos": len(grupos),
                    "registros_a_remover": total_repetidos,
                }

            if not silent and not simular and not confirmar_automaticamente:
                resposta = console.input(
                    "[bold red]Confirmar a exclusão dos registros repetidos? [s/N]:[/bold red] "
                ).strip().casefold()
                if resposta not in ("s", "sim", "y", "yes"):
                    conexao.rollback()
                    console.print("[yellow]Operação cancelada; nenhuma alteração foi gravada.[/yellow]\n")
                    return {"success": False, "cancelado": True}

            tabela = None
            if not silent:
                tabela = Table(box=box.ROUNDED, border_style="blue", show_lines=True)
                tabela.add_column("#", justify="right", style="dim")
                tabela.add_column("d_r1", justify="right")
                tabela.add_column("d_p", justify="right")
                tabela.add_column("d_r2", justify="right")
                tabela.add_column("d_literal", justify="right")
                tabela.add_column("Preservado", justify="right", style="bold green")
                tabela.add_column("Excluídos", style="red", overflow="fold")

            detalhes = []
            removidos = 0
            for indice, grupo in enumerate(grupos, 1):
                ids = ids_do_grupo(cursor, grupo)
                id_preservado = min(ids)
                ids_excluir = [id_d for id_d in ids if id_d != id_preservado]

                afetados = len(ids_excluir) if simular else excluir_ids(cursor, ids_excluir)
                removidos += afetados
                detalhe = {
                    "d_r1": grupo["d_r1"],
                    "d_p": grupo["d_p"],
                    "d_r2": grupo["d_r2"],
                    "d_literal": grupo["d_literal"],
                    "id_preservado": id_preservado,
                    "ids_excluidos": ids_excluir,
                    "total_excluido": afetados,
                }
                detalhes.append(detalhe)

                if tabela is not None:
                    tabela.add_row(
                        str(indice),
                        valor(grupo["d_r1"]),
                        valor(grupo["d_p"]),
                        valor(grupo["d_r2"]),
                        valor(grupo["d_literal"]),
                        str(id_preservado),
                        ", ".join(map(str, ids_excluir)),
                    )

            if simular:
                conexao.rollback()
            else:
                conexao.commit()

            if tabela is not None:
                console.print(tabela)
                if simular:
                    console.print(f"[bold yellow]Simulação concluída: {removidos:,} registro(s) seriam removidos.[/bold yellow]\n".replace(",", "."))
                else:
                    console.print(f"[bold green]✓ {removidos:,} registro(s) duplicado(s) removido(s).[/bold green]\n".replace(",", "."))

            return {
                "success": True,
                "simulacao": simular,
                "grupos": len(grupos),
                "removidos": removidos,
                "detalhes": detalhes,
            }
    except Exception:
        conexao.rollback()
        raise
    finally:
        conexao.close()


def run(parametros=None, chat=None, silent=False):
    try:
        resultado = executar_remocao(parametros=parametros, silent=silent)
    except KeyboardInterrupt:
        if not silent:
            console.print("\n[yellow]Operação interrompida; nenhuma alteração foi gravada.[/yellow]\n")
            return None
        return {"success": False, "interrompido": True}
    except Exception as erro:
        if not silent:
            console.print(f"[bold red]Erro ao remover duplicatas de rdf_data:[/bold red] {erro}\n")
            return None
        return {"success": False, "error": str(erro)}

    if silent:
        return resultado
    return None


if __name__ == "__main__":
    run(parametros=sys.argv[1:])
