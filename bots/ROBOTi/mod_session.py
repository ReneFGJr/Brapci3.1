import os
import time
from datetime import datetime


def limpar_sessoes_antigas(diretorio):
    """
    Remove arquivos do diretório especificado que começam com 'ci_session'
    e que não foram criados na data atual.
    """
    hoje = datetime.now().date()

    try:
        for arquivo in os.listdir(diretorio):
            caminho_completo = os.path.join(diretorio, arquivo)

            # Verifica se é um arquivo e se começa com 'ci_session'
            if os.path.isfile(caminho_completo) and arquivo.startswith(
                    "ci_session"):
                # Obtém a data de criação do arquivo
                timestamp_criacao = os.path.getctime(caminho_completo)
                data_criacao = datetime.fromtimestamp(timestamp_criacao).date()

                # Remove se não foi criado hoje
                if data_criacao != hoje:
                    os.remove(caminho_completo)
                    print(f"Removido: {arquivo}")

        print("Limpeza concluída.")

    except Exception as e:
        print(f"Erro ao limpar sessões: {e}")
