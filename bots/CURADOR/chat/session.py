#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import shlex
from collections import deque

from chat.router import localizar
from tasks.executor import executar


def iniciar(first_message=None):

    print("\nDigite 'sair' para terminar.\n")

    ultimos_comandos = deque(maxlen=5)

    while True:

        #
        # Primeira mensagem (quando vem da linha de comando)
        #

        if first_message is not None:

            pergunta = first_message
            first_message = None

            print(f"CURADOR > {pergunta}")

        else:

            if ultimos_comandos:
                print("Últimos comandos executados:")
                for indice, comando_anterior in enumerate(
                    reversed(ultimos_comandos),
                    1
                ):
                    print(f"  {indice}. {comando_anterior}")
                print()

            pergunta = input("CURADOR > ").strip()

        #
        # Sai
        #

        if pergunta.lower() in (
            "sair",
            "exit",
            "quit"
        ):
            break

        #
        # Procura uma tarefa
        #

        task = localizar(pergunta)

        if task is not None:

            # Registra somente comandos reconhecidos e efetivamente executados.
            if not ultimos_comandos or ultimos_comandos[-1] != pergunta:
                ultimos_comandos.append(pergunta)

            #
            # Divide a linha preservando textos entre aspas
            #

            partes = shlex.split(pergunta)

            #
            # Remove o comando e envia apenas os parâmetros
            #

            parametros = partes[1:]

            resposta = executar(
                codigo=task,
                parametros=parametros,
                chat=pergunta
            )

            if resposta:

                print()
                print(resposta)
                print()

            continue

        #
        # Comando desconhecido
        #

        print("\nErro: o comando não existe.\n")
