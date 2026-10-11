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
            limite = time.monotonic() + 0.30
            while time.monotonic() < limite and len(sequencia) < 2:
                if not msvcrt.kbhit():
                    time.sleep(0.005)
                    continue
                sequencia += msvcrt.getwch()
            if sequencia in ("[A", "OA") and historico:
                posicao_historico = max(0, posicao_historico - 1)
                texto = historico[posicao_historico]
                _redesenhar_prompt(rotulo, texto)
            elif sequencia in ("[B", "OB") and historico:
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

    def ler_caractere():
        primeiro = os.read(descritor, 1)
        if not primeiro:
            return ""
        tamanho = 1
        valor = primeiro[0]
        if valor & 0xE0 == 0xC0:
            tamanho = 2
        elif valor & 0xF0 == 0xE0:
            tamanho = 3
        elif valor & 0xF8 == 0xF0:
            tamanho = 4
        restante = os.read(descritor, tamanho - 1) if tamanho > 1 else b""
        return (primeiro + restante).decode("utf-8", errors="replace")

    sys.stdout.write(rotulo)
    sys.stdout.flush()

    try:
        tty.setraw(descritor)
        while True:
            tecla = ler_caractere()

            if tecla == "\x1b" and select.select([descritor], [], [], 0.30)[0]:
                sequencia = ler_caractere()
                if sequencia in ("[", "O"):
                    codigo = ler_caractere()
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
    comando_pendente = None

    while True:

        #
        # Primeira mensagem (quando vem da linha de comando)
        #

        if comando_pendente is not None:

            pergunta = comando_pendente
            comando_pendente = None

            print(f"CURADOR > {pergunta}")

        elif first_message is not None:

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

            # Menus podem devolver um comando para o buffer do prompt.
            if (
                isinstance(resposta, dict)
                and resposta.get("_prompt_command")
                and resposta.get("command")
            ):
                comando_pendente = str(resposta["command"]).strip()
                continue

            if resposta:

                print()
                print(resposta)
                print()

            continue

        #
        # Comando desconhecido
        #

        print("\nErro: o comando não existe.\n")
