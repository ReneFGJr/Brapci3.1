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
        if first_message:
            pergunta = first_message
            first_message = None
            print(f"CURADOR > {pergunta}")

        else:
            pergunta = input("CURADOR > ")

        if pergunta.lower() in ["sair", "exit", "quit"]:
            break

        #
        # Procura uma tarefa
        #
        task = localizar(pergunta)


        if task is not None:
            resposta = executar(task, [])
            print(resposta)
            continue

        if task:
            print(f"\n▶ Executando tarefa {task['id']:04d}\n")
            executar(task["id"])
            continue

        #
        # Chat IA
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