import mod_embening

print("JUNIOR AI v0.1.0")
print("================")

# Abrindo o arquivo no modo de leitura
with open('txt/sample.txt', 'r', encoding='utf-8') as arquivo:
    # Lendo o conteúdo do arquivo
    txt = arquivo.read()

txt = txt.lower()
rx = mod_embening.registrar(txt)
print(rx)