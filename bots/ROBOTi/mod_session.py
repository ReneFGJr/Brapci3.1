import os
from datetime import datetime


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
