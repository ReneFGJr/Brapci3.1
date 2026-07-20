import importlib


def executar(
    codigo,
    parametros=None,
    chat=None,
    silent=False
):

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
                chat=chat,
                silent=silent
            )

        return {
            "success": False,
            "error": f"Tarefa {codigo:04d} não possui função run()."
        }

    except ModuleNotFoundError:

        return {
            "success": False,
            "error": f"Tarefa {codigo:04d} não encontrada."
        }

    except Exception as e:

        return {
            "success": False,
            "error": str(e)
        }