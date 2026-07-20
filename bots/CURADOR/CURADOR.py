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
import json
import sys

from config import APP_VERSION
from lib.banner import show as banner
from chat.session import iniciar as iniciar_chat
from chat.router import localizar
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

    #
    # Sem parâmetros -> modo interativo
    #

    if args.comando is None:

        banner()

        iniciar_chat()

        return

    comando = args.comando.strip()

    #
    # Tarefa numérica
    #

    if comando.isdigit():

        resultado = executar(
            int(comando),
            parametros=args.parametros,
            chat=None,
            silent=True
        )

        print(
            json.dumps(
                resultado,
                indent=4,
                ensure_ascii=False
            )
        )

        return

    #
    # Comandos internos
    #

    comandos = {

        ##"ajuda": "ajuda",
        ##"help": "ajuda",

        "tarefas": "tarefas",
        "tasks": "tarefas",

        "status": "status",

        "exit": "sair",
        "quit": "sair",
        "sair": "sair"

    }

    if comando.lower() in comandos:

        banner()

        iniciar_chat(
            first_message=comandos[comando.lower()]
        )

        return

    #
    # Localiza a tarefa pelo router
    #

    texto = " ".join(
        [comando] + args.parametros
    )

    codigo = localizar(texto)

    if codigo is None:

        print(
            json.dumps(
                {
                    "success": False,
                    "error": "Comando não reconhecido."
                },
                indent=4,
                ensure_ascii=False
            )
        )

        return

    #
    # Executa a tarefa
    #

    resultado = executar(
        codigo,
        parametros=args.parametros,
        chat=None,
        silent=True
    )

    print(
        json.dumps(
            resultado,
            indent=4,
            ensure_ascii=False
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