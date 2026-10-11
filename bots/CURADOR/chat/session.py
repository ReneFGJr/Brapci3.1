#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import shlex
from collections import deque
import os
import sys
import time

from chat.router import localizar
from tasks.executor import executar


def _redesenhar_prompt(rotulo, texto):
    sys.stdout.write(f"\r\033[2K{rotulo}{texto}")
    sys.stdout.flush()


def _ler_prompt_windows(rotulo, historico):
    import msvcrt

    texto = ""
    posicao_historico = len(historico)
    sys.stdout.write(rotulo)
    sys.stdout.flush()

    while True:
        tecla = msvcrt.getwch()

        if tecla in ("\x00", "\xe0"):
            codigo = msvcrt.getwch()
            if codigo == "H" and historico:
                posicao_historico = max(0, posicao_historico - 1)
                texto = historico[posicao_historico]
                _redesenhar_prompt(rotulo, texto)
            elif codigo == "P" and historico:
                posicao_historico = min(len(historico), posicao_historico + 1)
                texto = "" if posicao_historico == len(historico) else historico[posicao_historico]
                _redesenhar_prompt(rotulo, texto)
            continue

        if tecla == "\x1b":
            sequencia = ""
            for _ in range(5):
                if msvcrt.kbhit():
                    sequencia += msvcrt.getwch()
                    continue
                time.sleep(0.01)
            if sequencia == "[A" and historico:
                posicao_historico = max(0, posicao_historico - 1)
                texto = historico[posicao_historico]
                _redesenhar_prompt(rotulo, texto)
            elif sequencia == "[B" and historico:
                posicao_historico = min(len(historico), posicao_historico + 1)
                texto = "" if posicao_historico == len(historico) else historico[posicao_historico]
                _redesenhar_prompt(rotulo, texto)
            continue

        if tecla == "\r":
            print()
            return texto.strip()
        if tecla == "\x03":
            raise KeyboardInterrupt
        if tecla == "\x04":
            raise EOFError
        if tecla in ("\b", "\x7f"):
            texto = texto[:-1]
            _redesenhar_prompt(rotulo, texto)
            continue
        if tecla.isprintable():
            texto += tecla
            posicao_historico = len(historico)
            _redesenhar_prompt(rotulo, texto)


def _ler_prompt_posix(rotulo, historico):
    import select
    import termios
    import tty

    descritor = sys.stdin.fileno()
    configuracao = termios.tcgetattr(descritor)
    texto = ""
    posicao_historico = len(historico)
    sys.stdout.write(rotulo)
    sys.stdout.flush()

    try:
        tty.setraw(descritor)
        while True:
            tecla = sys.stdin.read(1)

            if tecla == "\x1b" and select.select([sys.stdin], [], [], 0.05)[0]:
                sequencia = sys.stdin.read(1)
                if sequencia == "[":
                    codigo = sys.stdin.read(1)
                    if codigo == "A" and historico:
                        posicao_historico = max(0, posicao_historico - 1)
                        texto = historico[posicao_historico]
                        _redesenhar_prompt(rotulo, texto)
                    elif codigo == "B" and historico:
                        posicao_historico = min(len(historico), posicao_historico + 1)
                        texto = "" if posicao_historico == len(historico) else historico[posicao_historico]
                        _redesenhar_prompt(rotulo, texto)
                continue

            if tecla in ("\r", "\n"):
                sys.stdout.write("\r\n")
                return texto.strip()
            if tecla == "\x03":
                raise KeyboardInterrupt
            if tecla == "\x04":
                raise EOFError
            if tecla in ("\x08", "\x7f"):
                texto = texto[:-1]
                _redesenhar_prompt(rotulo, texto)
                continue
            if tecla.isprintable():
                texto += tecla
                posicao_historico = len(historico)
                _redesenhar_prompt(rotulo, texto)
    finally:
        termios.tcsetattr(descritor, termios.TCSADRAIN, configuracao)


def ler_prompt(rotulo, historico):
    if not sys.stdin.isatty():
        return input(rotulo).strip()
    if os.name == "nt":
        return _ler_prompt_windows(rotulo, historico)
    return _ler_prompt_posix(rotulo, historico)


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

            pergunta = ler_prompt("CURADOR > ", list(ultimos_comandos))

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
