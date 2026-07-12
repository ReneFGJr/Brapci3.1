from pathlib import Path
import json
import os
import sys

import database
import sys_io

import ai_email
import ai_url
import ai_doi_handle
import ai_metadados
import ai_cited
import ai_keywords
import ai_section
import ai_abstract

import mod_abstract
import mod_small_world

from colorama import Fore, Style


BASE_DIR = Path("/data/Brapci3.1/bots/TOOLS")
PUBLIC_DIR = Path("/data/Brapci3.1/public")

def logo():
    print("<pre>")
    print("═" * 57)

    print(f"{'AI TOOLS':^57}")
    print(f"{'Version ' + version():^57}")
    print()

    logo = [
        (" █████╗ ██╗ ", "v.0.26.07.11                               "),
        ("██╔══██╗██║ ", "████████╗ ██████╗  ██████╗ ██╗     ███████╗"),
        ("██║  ██║██║ ", "╚══██╔══╝██╔═══██╗██╔═══██╗██║     ██╔════╝"),
        ("███████║██║ ", "   ██║   ██║   ██║██║   ██║██║     ███████╗"),
        ("██╔══██║██║ ", "   ██║   ██║   ██║██║   ██║██║     ╚════██║"),
        ("██║  ██║██║ ", "   ██║   ╚██████╔╝╚██████╔╝███████╗███████║"),
        ("╚═╝  ╚═╝╚═╝ ", "   ╚═╝    ╚═════╝  ╚═════╝ ╚══════╝╚══════╝"),
    ]

    for left, right in logo:

        # texto principal
        print(
            left +
            "  " +
            right
        )

    print("═" * 57)
    print("</pre>")

def version():
    return "v0.26.06.21"


def save_json(filename, data):
    with open(filename, "w", encoding="utf-8") as f:
        json.dump(data, f, ensure_ascii=False, indent=4)


def todo():
    sql = """
        SELECT ID
        FROM brapci_elastic.dataset
        WHERE JOURNAL = 75
          AND (ABSTRACT = '' OR KEYWORDS = '')
    """

    rows = database.query(sql)

    with open("toDO.sh", "w", encoding="utf-8") as f:
        for row in rows:
            f.write(f"python3 ai.py All {row[0]}\n")

    print("Arquivo gerado: toDO.sh")


class Document:

    def __init__(self, doc_id):

        self.id = int(doc_id)

        self.pdf = PUBLIC_DIR / sys_io.getNameFile(self.id)

        if not self.pdf.exists():
            raise FileNotFoundError(self.pdf)

        self.txt_file = Path(sys_io.getNameFileTXT(str(self.pdf)))
        print("TXT file:", self.txt_file)

        if not self.txt_file.exists():
            raise FileNotFoundError(self.txt_file)

        self.txt = sys_io.readfile(str(self.txt_file))

        if not self.txt:
            raise Exception(f"Erro lendo {self.txt_file}")

    def json_name(self, suffix):
        return str(self.pdf).replace(".pdf", suffix)


#########################################################
# AÇÕES
#########################################################

def action_email(doc):
    data = ai_email.extrair_emails(doc.txt)
    save_json(doc.json_name("_email.json"), data)


def action_url(doc):
    data = ai_url.extrair_urls(doc.txt)
    save_json(doc.json_name("_url.json"), data)


def action_doi(doc):
    ai_doi_handle.doi(doc.txt, doc.id)

    data = ai_doi_handle.extrair_doi(doc.txt)
    save_json(doc.json_name("_doi.json"), data)


def action_handle(doc):
    data = ai_doi_handle.extrair_handle(doc.txt)
    save_json(doc.json_name("_handle.json"), data)


def action_metadata(doc):
    data = ai_metadados.extrair_secoes_method_01(doc.txt)
    save_json(doc.json_name("_metadados.json"), data)


def action_cited(doc):
    ai_cited.extrair_referencias_v2(doc.id)


def action_section(doc):
    ai_section.extrair_sessao(doc.txt, doc.id)


def action_keywords(doc):
    data = ai_keywords.extract_keywords(doc.txt, doc.id)
    save_json(doc.json_name("_keywords.json"), data)

def action_keywords_ia(doc):
    mod_abstract.main(doc.id, keywords=True)

def action_abstract(doc):
    result = ai_abstract.extract_abstract(doc.txt, doc.id)

    if not result:
        mod_abstract.main(doc.id)


def action_abstract_ia(doc):
    mod_abstract.main(doc.id)


def action_docling(doc,force=False):

    import mod_docling

    print(Fore.YELLOW+f"\n== Executando pipeline Docling =========================================="+Fore.RESET)

    mod_docling.save_file_docling(str(doc.pdf), doc.id, force=force)


def action_all(doc):

    print(Fore.YELLOW+f"\n== Executando pipeline completa =========================================="+Fore.RESET)

    action_docling(doc, True)
    action_email(doc)
    action_url(doc)
    action_doi(doc)
    action_handle(doc)
    action_metadata(doc)
    action_cited(doc)
    action_section(doc)
    action_keywords(doc)
    action_abstract(doc)


#########################################################
# MAIN
#########################################################

ACTIONS = {
    "email": action_email,
    "url": action_url,
    "doi": action_doi,
    "handle": action_handle,
    "metadata": action_metadata,
    "cited": action_cited,
    "section": action_section,
    "keywords": action_keywords,
    "keywords_ia": action_keywords_ia,
    "abstract": action_abstract,
    "abstract_ia": action_abstract_ia,
    "docling": action_docling,
    "all": action_all,
}


def main():

    logo()

    os.chdir(BASE_DIR)

    if len(sys.argv) < 2:
        print("Uso:\n"
              "python ai.py <acao> <doc_id>\n"
              "Ações disponíveis: " + ", ".join(ACTIONS.keys()) + "\n"
              "Exemplo: python ai.py all 392175")
        sys.exit(1)
    else:
        action = sys.argv[1].lower()

        if action == "x":
            todo()
            return

        doc_id = int(sys.argv[2])

    if len(sys.argv) > 3:
        force_arg = str(sys.argv[3]).strip().lower()
        force = force_arg in ("1", "true", "yes", "y", "on")
    else:
        force = False
    print("[ Ação:", action, "Doc ID:", doc_id,' Force', force,']')

    if action in ("sw", "smallworld"):
        mod_small_world.proccess()
        return

    doc = Document(doc_id)

    if action not in ACTIONS:
        print("Ação inválida")
        print(", ".join(ACTIONS.keys()))
        return

    ACTIONS[action](doc, force) if action == "docling" else ACTIONS[action](doc)


if __name__ == "__main__":
    main()
