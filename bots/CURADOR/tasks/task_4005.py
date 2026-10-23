#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Verifica disponibilidade TCP dos servidores Z39.50 cadastrados."""

import socket
from contextlib import nullcontext

from rich.console import Console
from rich.live import Live
from rich.table import Table
from rich.text import Text

from lib.database import conectar

console = Console()
TASK = {
    "id": 4005,
    "name": "Z3950 Check",
    "description": "Testa host e porta dos servidores Z39.50 e atualiza sua disponibilidade.",
    "patterns": ["bibliographic z3950_check"],
    "parameters": [],
}


def checar_servidor(host, porta, timeout=5):
    """Testa conexão TCP; não valida negociação ou busca Z39.50."""
    try:
        porta = int(porta)
        if not 1 <= porta <= 65535:
            raise ValueError("Porta fora do intervalo 1–65535.")
        with socket.create_connection((host.strip(), porta), timeout=timeout):
            return True, None
    except (OSError, ValueError, TypeError) as exc:
        return False, str(exc)


def run(parametros=None, chat=None, silent=False):
    conexao = None
    cursor = None
    resultados = []

    def painel(atual, total, nome, situacao):
        tabela = Table(title="Verificação TCP dos servidores Z39.50")
        tabela.add_column("Progresso")
        tabela.add_column("Servidor")
        tabela.add_column("Situação")
        tabela.add_row(f"{atual}/{total}", nome, situacao)
        ativos = sum(item["active"] for item in resultados)
        tabela.caption = f"Disponíveis: {ativos} | Indisponíveis: {len(resultados) - ativos}"
        return tabela

    try:
        conexao = conectar()
        cursor = conexao.cursor(dictionary=True)
        cursor.execute(
            "SELECT id, name, host, port FROM `brapci_z39.50`.`z3950_servers` "
            "WHERE host IS NOT NULL AND TRIM(host) <> '' "
            "AND port IS NOT NULL AND port > 0 ORDER BY id"
        )
        servidores = cursor.fetchall()
        display = nullcontext() if silent else Live(
            painel(0, len(servidores), "", "Iniciando"), console=console
        )
        with display as live:
            for indice, servidor in enumerate(servidores, 1):
                if live:
                    live.update(painel(indice, len(servidores), servidor["name"], "Conectando..."), refresh=True)
                ativo, erro = checar_servidor(servidor["host"], servidor["port"])
                cursor.execute(
                    "UPDATE `brapci_z39.50`.`z3950_servers` "
                    "SET active = %s, last_active = NOW() WHERE id = %s",
                    (int(ativo), servidor["id"]),
                )
                conexao.commit()
                resultados.append({
                    "id": servidor["id"], "name": servidor["name"],
                    "host": servidor["host"], "port": servidor["port"],
                    "active": ativo, "error": erro,
                })
                if live:
                    live.update(painel(indice, len(servidores), servidor["name"], "Disponível" if ativo else erro), refresh=True)
            if live:
                live.update(painel(len(servidores), len(servidores), "", "Finalizado"), refresh=True)
        ativos = sum(item["active"] for item in resultados)
        resultado = {
            "success": True, "total": len(resultados), "ativos": ativos,
            "inativos": len(resultados) - ativos, "servidores": resultados,
        }
        if not silent:
            falhas = [item for item in resultados if not item["active"]]
            if falhas:
                tabela = Table(title="Erros na validação Z39.50")
                tabela.add_column("Servidor", overflow="fold")
                tabela.add_column("Host:porta", overflow="fold")
                tabela.add_column("Erro", overflow="fold", style="red")
                for item in falhas:
                    tabela.add_row(
                        Text(item["name"]),
                        Text(f"{item['host']}:{item['port']}"),
                        Text(item["error"] or "Falha de conexão sem detalhes."),
                    )
                console.print(tabela)
            elif not servidores:
                console.print("Nenhum servidor com host e porta preenchidos para validar.")
        return resultado if silent else None
    except Exception as exc:
        if conexao is not None:
            conexao.rollback()
        resultado = {"success": False, "error": str(exc), "servidores": resultados}
        if not silent:
            console.print(resultado)
            return None
        return resultado
    finally:
        if cursor is not None:
            cursor.close()
        if conexao is not None:
            conexao.close()
