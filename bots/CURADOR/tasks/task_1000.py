# tasks/task_1000.py

TASK = {
    "id": 1000,
    "name": "Coleta de Revistas",
    "description": "Realiza a coleta de periódicos cadastrados na BRAPCI.",
    "patterns": [
        "coletar revistas",
        "iniciar coleta",
        "atualizar periódicos",
        "buscar novas revistas",
        "rodar harvesting"
    ],
    "parameters": [
        {
            "name": "fonte",
            "type": "string",
            "required": False
        }
    ]
}


def run(parametros=None,chat=None,silent=False):

    print("Iniciando coleta...")

    # coleta