#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import shlex

from lib.ai import perguntar
from chat.memory import Conversation
from chat.router import localizar
from tasks.executor import executar


def iniciar(first_message=None):

    conversa = Conversation()

    print("\nDigite 'sair' para terminar.\n")

    while True:

        #
        # Primeira mensagem (quando vem da linha de comando)
        #

        if first_message is not None:

            pergunta = first_message
            first_message = None

            print(f"CURADOR > {pergunta}")

        else:

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
        # Conversa com a IA
        #

        conversa.user(pergunta)

        resposta = perguntar(
            conversa.prompt()
        )

        conversa.assistant(resposta)

        print()
        print(resposta)
        print()

    conversa.save()