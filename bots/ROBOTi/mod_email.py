import smtplib
import ssl
from email.message import EmailMessage
from dotenv import load_dotenv
import os
from pathlib import Path

def send_email(to_email, subject, body, debug=True):
    # Caminho relativo ao .env (../../.env)
    env_path = Path(__file__).resolve().parents[2] / '.env'
    print("Arquivo de configuração do e-mail: ", env_path)

    if not env_path.exists():
        print(f"❌ Arquivo .env não encontrado em: {env_path}")
        return  # Ou: raise FileNotFoundError(f".env não encontrado: {env_path}")

    # Carrega o .env de ../../.env
    env_path = Path(__file__).resolve().parents[2] / '.env'
    load_dotenv(dotenv_path=env_path)

    smtp_server = os.getenv("EMAIL_SMTP")
    smtp_port = int(os.getenv("EMAIL_SMTP_PORT", 465))
    sender_email = os.getenv("EMAIL_FROM")
    sender_password = os.getenv("EMAIL_PASSWORD")
    user_auth = os.getenv("EMAIL_USER_AUTH")

    if debug:
        print("🔧 Carregando configurações do e-mail:")
        print(f"  SMTP Server: {smtp_server}")
        print(f"  SMTP Port: {smtp_port}")
        print(f"  Remetente: {sender_email}")
        print(f"  Usuário Auth: {user_auth}")
        print(f"  Destinatário: {to_email}")

    msg = EmailMessage()
    msg["Subject"] = subject
    msg["From"] = sender_email
    msg["To"] = to_email
    msg.set_content(body)

    if debug:
        print("\n📨 Criando a mensagem do e-mail:")
        print(f"  Assunto: {subject}")
        print(f"  Corpo: {body}")

    context = ssl.create_default_context()
    try:
        if debug:
            print("\n🔐 Iniciando conexão segura com o servidor SMTP...")
        with smtplib.SMTP_SSL(smtp_server, smtp_port, context=context) as server:
            server.set_debuglevel(1)  # <-- Ativa o modo verbose do SMTP
            if debug:
                print("🔑 Realizando login no servidor SMTP...")
            server.login(user_auth, sender_password)
            if debug:
                print("📤 Enviando a mensagem...")
            server.send_message(msg)
        if debug:
            print(f"✅ E-mail enviado com sucesso para {to_email}")
    except Exception as e:
        print(f"❌ Erro ao enviar o e-mail: {e}")

if __name__ == "__main__":
    print("Iniciando o envio de e-mail...")
    send_email("renefgj@gmail.com", "Assunto do Email", "Corpo da mensagem")