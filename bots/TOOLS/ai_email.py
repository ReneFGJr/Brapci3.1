import re
import sys_io

def extrair_emails(texto):
    # Expressão regular para detectar e-mails
    padrao_email = r'[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}'

    # Busca todos os e-mails no texto
    emails = re.findall(padrao_email, texto)

    return emails
id = 309177
file = sys_io.getNameFile(id)
print("File",file)
txt = sys_io.readfile(file)
print(txt)