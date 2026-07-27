#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Task Model
"""

from pathlib import Path
import json

from rich.console import Console
from rich.table import Table

console = Console()

TASK = {
    "id": -1,
    "name": "Model",
    "description": "Mostra a modelo de TASK.",
    "patterns": [
        "model",
    ],
    "parameters": [
        {
            "name": "fonte",
            "type": "string",
            "required": False
        }
    ]
}


def erro(mensagem):

    return {
        "success": False,
        "error": mensagem
    }



def run(
    parametros=None,
    chat=None,
    silent=False
):

    if parametros is None:

        parametros = []

    return ajuda(
        silent=silent
    )
