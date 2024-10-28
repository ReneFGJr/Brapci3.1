import re
import json
import sys
import sys_io

def version():
    return "v0.24.10.27"

def extrair_emails(texto):
    # Expressão regular para detectar e-mails
    padrao_email = r'[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}'

    # Busca todos os e-mails no texto
    emails = re.findall(padrao_email, texto)

    return emails


########################################### Início
print("TOOLS e-mail",version())
print("===============================================")

if (len(sys.argv) > 1):
    parm = sys.argv
    id = parm[1]
else:
    id = 309177

file = sys_io.getNameFile(id)
txt = sys_io.readfile(file)
emails = extrair_emails(txt)

fileN = file.replace('.txt','_email.json')
# Salva a lista em um arquivo JSON
with open(fileN, "w", encoding="utf-8") as arquivo:
    json.dump(emails, arquivo, ensure_ascii=False, indent=4)
