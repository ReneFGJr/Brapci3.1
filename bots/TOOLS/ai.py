import re
import os
import json
import sys
import sys_io

import ai_email
import ai_url
import ai_doi_handle
import ai_metadados
import ai_cited
import ai_keywords
import ai_section
import mod_convert_repository

def version():
    """Retorna a versão do script."""
    return "v0.24.10.27"

def extrair_emails(texto):
    """Extrai e-mails de um texto usando regex."""
    padrao_email = r'[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}'
    return re.findall(padrao_email, texto)

def save_file(file_path, data):
    """Salva dados em um arquivo JSON."""
    with open(file_path, "w", encoding="utf-8") as arquivo:
        json.dump(data, arquivo, ensure_ascii=False, indent=4)
    return True

def help():
    print("HELP")
    print("Actions:")
    print("email: Extrai e-mails do texto.")
    print("url: Extrai URLs do texto.")
    print("doi: Extrai DOIs do texto.")
    print("handle: Extrai Handles do texto.")
    print("metadata: Extrai metadados do texto.")
    print("cited: Extrai referências citadas do texto.")
    print("section: Extrai seção do texto.")
    print("keywords: Extrai palavras-chave do texto.")


def process_action(action, text, file_output, doc_id=None):
    """Executa a ação especificada no texto e salva o resultado."""
    actions = {
        "email": (ai_email.extrair_emails, "_email.json"),
        "url": (ai_url.extrair_urls, "_url.json"),
        "doi": (ai_doi_handle.extrair_doi, "_doi.json"),
        "handle": (ai_doi_handle.extrair_handle, "_handle.json"),
        "metadata": (ai_metadados.extrair_secoes_method_01, "_metadados.json"),
        "cited": (lambda txt, doc_id: ai_cited.extrair_referencias(txt, doc_id), "_cited.json"),
        "section": (lambda txt, doc_id: ai_section.extrair_sessao(txt, doc_id), None),
        "keywords": (lambda txt, doc_id: ai_keywords.extract_keywords(txt, doc_id), "_keywords.json")
    }

    help()

    if action in actions:
        extractor, extension = actions[action]
        print(f"Executando ação: {action}")

        result = extractor(text, doc_id) if doc_id else extractor(text)

        if extension:
            output_file = file_output.replace(".txt", extension)
            save_file(output_file, result)
            print(f"Dados salvos em {output_file}")
    else:
        print("Ação não reconhecida.")
        print("Ações disponíveis:", list(actions.keys()))

# Execução principal
def main():
    print("TOOLS AI", version())
    print("=" * 47)

    base_dir = '/data/Brapci3.1/bots/TOOLS'
    os.chdir(base_dir)

    # Definir parâmetros
    act, doc_id = sys.argv[1:3] if len(sys.argv) > 1 else ('help', None)

    if doc_id is None:
        help()
        sys.exit()

    public_dir = '/data/Brapci3.1/public/'
    file_path = os.path.join(public_dir, sys_io.getNameFile(doc_id))
    file_txt = sys_io.getNameFileTXT(file_path)
    text_content = sys_io.readfile(file_txt)

    print(f"Arquivo de entrada: {file_path}")
    process_action(act, text_content, file_path, doc_id)

if __name__ == "__main__":
    main()