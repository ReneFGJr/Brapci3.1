#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
=========================================================
 CURADOR
 Sistema Inteligente de Curadoria Científica
=========================================================
Autor : Rene Gabriel Junior
"""

import argparse
import sys

from config import APP_VERSION
from lib.banner import show as banner
from chat.session import iniciar as iniciar_chat
from tasks.executor import executar


def main():

    parser = argparse.ArgumentParser(
        prog="CURADOR",
        description="Sistema Inteligente de Curadoria Científica"
    )

    parser.add_argument(
        "comando",
        nargs="?",
        help="Código da tarefa ou comando"
    )

    parser.add_argument(
        "parametros",
        nargs="*",
        help="Parâmetros adicionais"
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

    banner()

    #
    # Sem parâmetros → abre o Chat
    #

    if args.comando is None:
        iniciar_chat()
        return

    comando = args.comando.strip()

    #
    # Executa uma tarefa numérica
    #

    if comando.isdigit():

        executar(
            int(comando),
            args.parametros
        )
        return

    #
    # Comandos internos
    #

    comandos = {

        "ajuda": "ajuda",
        "help": "ajuda",

        "tarefas": "tarefas",
        "tasks": "tarefas",

        "status": "status",

        "exit": "sair",
        "quit": "sair",
        "sair": "sair"

    }

    if comando.lower() in comandos:

        iniciar_chat(
            first_message=comandos[comando.lower()]
        )

        return

    #
    # Linguagem natural
    #

    iniciar_chat(
        first_message=" ".join(
            [comando] + args.parametros
        )
    )


if __name__ == "__main__":

    try:

        main()

    except KeyboardInterrupt:

        print("\nEncerrando CURADOR...")

        sys.exit(0)

    except Exception as e:

        print("\nErro inesperado:")
        print(e)

        sys.exit(1)