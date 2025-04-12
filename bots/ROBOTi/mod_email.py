import smtplib
import ssl
from email.message import EmailMessage
from dotenv import load_dotenv
import os
from pathlib import Path

def send_email(to_email, subject, body, debug=True):
    from pathlib import Path
    from dotenv import load_dotenv

    env_path = Path(__file__).resolve().parents[2] / '.env'

    if not env_path.exists():
        print(f"❌ Arquivo .env não encontrado em: {env_path}")
        return  # Aborta a execução. Você pode usar 'raise' se preferir forçar erro.

    load_dotenv(dotenv_path=env_path)

    # ... (continua com o restante da função normalmente)


if __name__ == "__main__":
    print("Iniciando o envio de e-mail...")
    send_email("renefgj@gmail.com", "Assunto do Email", "Corpo da mensagem")