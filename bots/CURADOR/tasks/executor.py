import importlib


def executar(codigo, parametros=None, chat=None):

    if parametros is None:
        parametros = []

    modulo = f"tasks.task_{codigo:04d}"

    try:

        task = importlib.import_module(modulo)

        #
        # Executa a tarefa
        #

        if hasattr(task, "run"):

            return task.run(
                parametros=parametros,
                chat=chat
            )

        return f"Tarefa {codigo:04d} não possui função run()."

    except ModuleNotFoundError:

        return f"Tarefa {codigo:04d} não encontrada."

    except Exception as e:

        return f"Erro: {e}"