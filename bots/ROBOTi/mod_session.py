import os
import time
from datetime import datetime


def limpar_sessoes_antigas(diretorio, limite=10000):
    """
    Remove até 'limite' arquivos do diretório especificado que começam com 'ci_session'
    e que não foram criados na data atual.
    Ao final, exibe quantos arquivos ainda restam para remoção.
    """
    hoje = datetime.now().date()
    arquivos_removidos = 0

    try:
        # Lista todos os arquivos que começam com 'ci_session'
        arquivos = [
            f for f in os.listdir(diretorio) if f.startswith("ci_session")
        ]
        arquivos_restantes = 0

        for arquivo in arquivos:
            if arquivos_removidos >= limite:
                break

            caminho_completo = os.path.join(diretorio, arquivo)

            if os.path.isfile(caminho_completo):
                # Obtém a data de criação do arquivo
                timestamp_criacao = os.path.getctime(caminho_completo)
                data_criacao = datetime.fromtimestamp(timestamp_criacao).date()

                # Remove se não foi criado hoje
                if data_criacao != hoje:
                    os.remove(caminho_completo)
                    arquivos_removidos += 1
                    print(
                        f"Removido ({arquivos_removidos}/{limite}): {arquivo}")

        # Conta quantos arquivos antigos ainda existem
        for arquivo in os.listdir(diretorio):
            caminho_completo = os.path.join(diretorio, arquivo)
            if os.path.isfile(caminho_completo) and arquivo.startswith(
                    "ci_session"):
                timestamp_criacao = os.path.getctime(caminho_completo)
                data_criacao = datetime.fromtimestamp(timestamp_criacao).date()
                if data_criacao != hoje:
                    arquivos_restantes += 1

        print(
            f"Limpeza concluída. Total de arquivos removidos: {arquivos_removidos}"
        )
        print(
            f"Total de arquivos restantes para remoção: {arquivos_restantes}")

    except Exception as e:
        print(f"Erro ao limpar sessões: {e}")
