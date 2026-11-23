#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Limpa arquivos antigos das sessões das aplicações."""

import os
from datetime import datetime

from rich.console import Console


console = Console()

TASK = {
    "id": 1001,
    "name": "Limpeza de sessões",
    "description": "Remove arquivos de sessão que não foram modificados hoje.",
    "patterns": [
        "session clear",
        "system clear",
    ],
    "parameters": [],
}

SESSION_DIRECTORIES = (
    "/data/Brapci3.1/writable/session/",
    "/data/HumaniDados/v2/writable/session/",
)


def limpar_sessoes_antigas(diretorio, limite=20000):
    """
    Remove até ``limite`` arquivos de sessão que não foram modificados
    na data atual.

    Em Linux, ``getctime()`` informa a última alteração dos metadados do
    arquivo, e não sua criação. Por isso a expiração usa ``mtime``, que
    representa a última gravação da sessão.
    """
    hoje = datetime.now().date()
    arquivos_removidos = 0
    arquivos_elegiveis = 0
    erros = 0

    try:
        if limite < 0:
            raise ValueError("O limite não pode ser negativo")

        with os.scandir(diretorio) as entradas:
            for entrada in entradas:
                if not entrada.name.startswith("ci_session"):
                    continue

                try:
                    if not entrada.is_file(follow_symlinks=False):
                        continue

                    data_modificacao = datetime.fromtimestamp(
                        entrada.stat(follow_symlinks=False).st_mtime
                    ).date()

                    if data_modificacao >= hoje:
                        continue

                    arquivos_elegiveis += 1
                    if arquivos_removidos >= limite:
                        continue

                    os.remove(entrada.path)
                    arquivos_removidos += 1
                    print(
                        f"Removido ({arquivos_removidos}/{limite}): "
                        f"{entrada.name}"
                    )
                except OSError as erro:
                    erros += 1
                    print(f"Erro ao remover {entrada.name}: {erro}")

        arquivos_pendentes = arquivos_elegiveis - arquivos_removidos
        print(
            "Limpeza concluída. "
            f"Elegíveis: {arquivos_elegiveis}; "
            f"removidos: {arquivos_removidos}; "
            f"pendentes: {arquivos_pendentes}; erros: {erros}."
        )
        return arquivos_removidos, arquivos_pendentes, erros

    except (OSError, ValueError) as erro:
        print(f"Erro ao limpar sessões em {diretorio}: {erro}")
        return 0, 0, 1


def run(parametros=None, chat=None, silent=False):
    del parametros, chat

    resultados = []
    for diretorio in SESSION_DIRECTORIES:
        if not silent:
            console.print(f"[cyan]Limpando sessões em:[/cyan] {diretorio}")

        total_removidos = 0
        total_erros = 0
        pendentes = 0
        execucoes = 0

        while True:
            execucoes += 1
            removidos, pendentes, erros = limpar_sessoes_antigas(diretorio)
            total_removidos += removidos
            total_erros += erros

            if pendentes == 0:
                break

            if removidos == 0:
                if not silent:
                    console.print(
                        "[yellow]Limpeza interrompida: ainda existem "
                        f"{pendentes} arquivo(s), mas nenhum pôde ser removido "
                        "na última execução.[/yellow]"
                    )
                break

            if not silent:
                console.print(
                    f"[cyan]Ainda existem {pendentes} arquivo(s). "
                    "Executando uma nova limpeza...[/cyan]"
                )

        resultados.append(
            {
                "directory": diretorio,
                "removed": total_removidos,
                "pending": pendentes,
                "errors": total_erros,
                "runs": execucoes,
            }
        )

    return {
        "success": all(resultado["errors"] == 0 for resultado in resultados),
        "directories": resultados,
        "removed": sum(resultado["removed"] for resultado in resultados),
        "pending": sum(resultado["pending"] for resultado in resultados),
        "errors": sum(resultado["errors"] for resultado in resultados),
    }
