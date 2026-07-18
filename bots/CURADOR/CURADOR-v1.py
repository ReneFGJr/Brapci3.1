#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
============================================================
 CURADOR
 Sistema Inteligente de Curadoria Científica
------------------------------------------------------------
 Autor....: Rene Gabriel Junior
 Linguagem: Python 3
============================================================
"""

import argparse
import importlib
import sys

from config import APP_NAME, APP_VERSION
from chat.session import iniciar as iniciar_chat


# ----------------------------------------------------------
# Banner
# ----------------------------------------------------------

def banner():
    print("=" * 60)
    print(f" {APP_NAME}")
    print(" Sistema Inteligente de Curadoria")
    print(f" Versão {APP_VERSION}")
    print("=" * 60)


# ----------------------------------------------------------
# Executa uma tarefa
# ----------------------------------------------------------

def executar(codigo, parametros):

    modulo = f"tasks.task_{codigo:04d}"

    try:

        task = importlib.import_module(modulo)

        if hasattr(task, "run"):
            task.run(parametros)
        else:
            print(f"Tarefa {codigo:04d} inválida.")

    except ModuleNotFoundError:
        print(f"Tarefa {codigo:04d} não implementada.")

    except Exception as erro:
        print("Erro:")
        print(erro)


# ----------------------------------------------------------
# Main
# ----------------------------------------------------------

def main():

    parser = argparse.ArgumentParser(
        prog="CURADOR",
        description="Sistema Inteligente de Curadoria"
    )

    parser.add_argument(
        "codigo",
        nargs="?",
        type=int,
        help="Código da tarefa"
    )

    parser.add_argument(
        "parametros",
        nargs="*",
        help="Parâmetros da tarefa"
    )

    parser.add_argument(
        "--debug",
        action="store_true"
    )

    parser.add_argument(
        "--version",
        action="version",
        version=APP_VERSION
    )

    args = parser.parse_args()

    from lib.banner import show as banner
    banner()

    # ---------------------------------------------
    # Modo CHAT
    # ---------------------------------------------

    if args.codigo is None:

        iniciar_chat()
        return

    # ---------------------------------------------
    # Modo TAREFA
    # ---------------------------------------------

    executar(
        args.codigo,
        args.parametros
    )


# ----------------------------------------------------------

if __name__ == "__main__":

    try:

        main()

    except KeyboardInterrupt:

        print("\n\nCURADOR finalizado.")

        sys.exit(0)