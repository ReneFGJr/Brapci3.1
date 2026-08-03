#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Task 6200
Tasks do sistema.
"""

import requests

from rich.console import Console
from rich.table import Table

console = Console()

TASK = {
    "id": 6200,
    "name": "TASKS",
    "description": "Gerenciar tasks do sistema.",
    "patterns": ["tasks"],
    "parameters": [{
        "name": "nome",
        "type": "string",
        "required": True
    }]
}

import json
import requests
import pymysql
import sys

from pathlib import Path
import os

import pymysql
from dotenv import load_dotenv

# Localiza o .env na raiz do projeto
BASE_DIR = Path(__file__).resolve().parent.parent
load_dotenv(BASE_DIR / ".env")


def erro(mensagem):
    return {
        "success": False,
        "error": mensagem,
    }


def get_connection(database=None):
    """
    Retorna uma conexão MySQL utilizando as configurações do .env.

    Parameters
    ----------
    database : str | None
        Se informado, sobrescreve o banco definido no .env.
    """

    return pymysql.connect(
        host=os.getenv("DB_HOST", "localhost"),
        port=int(os.getenv("DB_PORT", 3306)),
        user=os.getenv("DB_USERNAME"),
        password=os.getenv("DB_PASSWORD"),
        database=database or os.getenv("DB_DATABASE"),
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=False,
    )

def task_add(task, silent=False):
    valid_tasks = {
        "ARTICLE": 5,
        "BOOK": 6,
        "HARVESTING": 1,
    }

    if task is None:
        if silent:
            return erro("Task inválida.")

        console.print("[bold red]Task inválida.[/bold red]")
        return False

    task_id = str(task).strip().upper()

    if task_id not in valid_tasks:
        mensagem = f"Task inválida: {task_id}. Use ARTICLE, BOOK ou HARVESTING."

        if silent:
            return erro(mensagem)

        console.print(f"[bold red]{mensagem}[/bold red]")
        return False

    priority = valid_tasks[task_id]
    conn = None

    try:
        conn = get_connection("brapci_bots")

        with conn.cursor() as cur:
            cur.execute(
                "SELECT id_task, task_id, task_propriry FROM tasks WHERE task_id = %s ORDER BY id_task DESC LIMIT 1",
                (task_id, ),
            )
            existente = cur.fetchone()

            if existente is not None:
                mensagem = (
                    f"Task já existe na fila: {existente.get('task_id')} "
                    f"(id={existente.get('id_task')}, priority={existente.get('task_propriry')})"
                )

                if silent:
                    return erro(mensagem)

                console.print(f"[yellow]{mensagem}[/yellow]")
                return False

            cur.execute(
                "INSERT INTO tasks (task_id, task_propriry) VALUES (%s, %s)",
                (task_id, priority),
            )
            conn.commit()
            new_id = cur.lastrowid

        result = {
            "success": True,
            "id": new_id,
            "task_id": task_id,
            "priority": priority,
        }

        if silent:
            return result

        console.print(
            f"[bold green]Task adicionada:[/bold green] {task_id} (priority={priority})"
        )
        return None

    except Exception as e:
        if conn is not None:
            conn.rollback()

        if silent:
            return erro(str(e))

        console.print(f"[bold red]Erro ao adicionar task:[/bold red] {e}")
        return False

    finally:
        if conn is not None:
            conn.close()

def task_list(silent=False):
    conn = None

    try:
        conn = get_connection("brapci_bots")

        with conn.cursor() as cur:
            cur.execute("SELECT * FROM tasks")
            rows = cur.fetchall()

        result = {
            "success": True,
            "total": len(rows),
            "tasks": rows,
        }

        if silent:
            return result

        if not rows:
            console.print("[yellow]Nenhuma task agendada encontrada.[/yellow]")
            return None

        preferred_columns = [
            "id_task",
            "task_id",
            "task_propriry",
            "created_at",
            "updated_at",
        ]

        available_columns = list(rows[0].keys())
        columns = [c for c in preferred_columns if c in available_columns]

        if not columns:
            columns = available_columns

        table = Table(
            title="TASKS - Atividades Agendadas",
            header_style="bold cyan",
            show_lines=True,
        )

        for column in columns:
            table.add_column(str(column), overflow="fold")

        for row in rows:
            table.add_row(*[str(row.get(c, "")) for c in columns])

        console.print(table)
        console.print(f"[bold green]Total:[/bold green] {len(rows)}")

        return None

    except Exception as e:
        if silent:
            return erro(str(e))

        console.print(f"[bold red]Erro ao listar tasks:[/bold red] {e}")
        return False

    finally:
        if conn is not None:
            conn.close()


def task_clear(silent=False):
    conn = None

    try:
        conn = get_connection("brapci_bots")

        with conn.cursor() as cur:
            cur.execute("SELECT COUNT(*) AS total FROM tasks")
            total_before = cur.fetchone().get("total", 0)

            cur.execute("DELETE FROM tasks")
            conn.commit()

        result = {
            "success": True,
            "deleted": int(total_before),
            "message": "Fila de tasks limpa com sucesso.",
        }

        if silent:
            return result

        console.print(
            f"[bold green]Fila limpa com sucesso.[/bold green] Registros removidos: {total_before}"
        )
        return None

    except Exception as e:
        if conn is not None:
            conn.rollback()

        if silent:
            return erro(str(e))

        console.print(f"[bold red]Erro ao limpar tasks:[/bold red] {e}")
        return False

    finally:
        if conn is not None:
            conn.close()


def task_delete(task, silent=False):
    if task is None:
        if silent:
            return erro("Task inválida.")

        console.print("[bold red]Task inválida.[/bold red]")
        return False

    task_id = str(task).strip().upper()

    if task_id == "":
        if silent:
            return erro("Task inválida.")

        console.print("[bold red]Task inválida.[/bold red]")
        return False

    conn = None

    try:
        conn = get_connection("brapci_bots")

        with conn.cursor() as cur:
            cur.execute(
                "SELECT COUNT(*) AS total FROM tasks WHERE task_id = %s",
                (task_id,),
            )
            total = int(cur.fetchone().get("total", 0))

            if total == 0:
                mensagem = f"Nenhum item encontrado para task_id={task_id}."

                if silent:
                    return erro(mensagem)

                console.print(f"[yellow]{mensagem}[/yellow]")
                return False

            cur.execute(
                "DELETE FROM tasks WHERE task_id = %s",
                (task_id,),
            )
            conn.commit()

        result = {
            "success": True,
            "task_id": task_id,
            "deleted": total,
        }

        if silent:
            return result

        console.print(
            f"[bold green]Task removida:[/bold green] {task_id} | Registros removidos: {total}"
        )
        return None

    except Exception as e:
        if conn is not None:
            conn.rollback()

        if silent:
            return erro(str(e))

        console.print(f"[bold red]Erro ao remover task:[/bold red] {e}")
        return False

    finally:
        if conn is not None:
            conn.close()


def task_cron(silent=False):
    conn = None

    try:
        conn = get_connection("brapci_bots")

        with conn.cursor() as cur:
            cur.execute("SELECT * FROM cron")
            rows = cur.fetchall()

        result = {
            "success": True,
            "total": len(rows),
            "cron": rows,
        }

        if silent:
            return result

        if not rows:
            console.print("[yellow]Nenhum registro encontrado na tabela cron.[/yellow]")
            return None

        preferred_columns = [
            "id_cron",
            "cron_acron",
            "cron_name",
            "cron_cmd",
            "cron_day",
            "cron_exec",
            "cron_timeout",
            "cron_prior",
            "update_at",
            "created_at",
        ]

        available_columns = list(rows[0].keys())
        columns = [c for c in preferred_columns if c in available_columns]

        if not columns:
            columns = available_columns

        table = Table(
            title="CRON - Agendamentos",
            header_style="bold cyan",
            show_lines=True,
        )

        for column in columns:
            table.add_column(str(column), overflow="fold")

        for row in rows:
            table.add_row(*[str(row.get(c, "")) for c in columns])

        console.print(table)
        console.print(f"[bold green]Total:[/bold green] {len(rows)}")

        return None

    except Exception as e:
        if silent:
            return erro(str(e))

        console.print(f"[bold red]Erro ao listar cron:[/bold red] {e}")
        return False

    finally:
        if conn is not None:
            conn.close()



def run(parametros=None, chat=None, silent=False):
    if parametros is None:
        parametros = []

    if not silent:
        console.print()
        console.rule("[bold blue]TASKS[/bold blue]")

    if len(parametros) == 0:
        if silent:
            return erro("Informar o tipo da tarefa.")

        console.print("[bold red]Informar o tipo da tarefa.[/bold red]")
        console.print()
        console.print("Exemplo:")
        console.print("    TASK LIST")
        return False

    print(f"==========", parametros[0].upper())

    if parametros[0].upper() == "LIST":
        return task_list(silent=silent)

    if parametros[0].upper() == "CLEAR":
        return task_clear(silent=silent)

    if parametros[0].upper() == "CRON":
        return task_cron(silent=silent)

    if parametros[0].upper() == "DEL":
        if len(parametros) < 2:
            if silent:
                return erro("Informe a task. Exemplo: TASK DEL ARTICLE")

            console.print(
                "[bold red]Informe a task. Exemplo: TASK DEL ARTICLE[/bold red]"
            )
            return False

        return task_delete(parametros[1], silent=silent)

    if parametros[0].upper() == "ADD":
        if len(parametros) < 2:
            if silent:
                return erro("Informe a task. Exemplo: TASK ADD ARTICLE")

            console.print("[bold red]Informe a task. Exemplo: TASK ADD ARTICLE[/bold red]")
            return False

        return task_add(parametros[1], silent=silent)

    if silent:
        return erro("Tipo de tarefa inválido. Use LIST, ADD, DEL, CLEAR ou CRON.")

    console.print("[bold red]Tipo de tarefa inválido. Use LIST, ADD, DEL, CLEAR ou CRON.[/bold red]")
    return False


if __name__ == "__main__":
    run(parametros=sys.argv[2:], silent=False)
