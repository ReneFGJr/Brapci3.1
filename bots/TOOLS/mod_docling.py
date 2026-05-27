from pathlib import Path
from docling.document_converter import DocumentConverter
import shutil
import logging
import re
import sys
from typing import Optional

# =========================================================
# CONFIGURAÇÕES
# =========================================================

BASE_DIR = Path("/data/Brapci3.1/public")

logging.basicConfig(level=logging.INFO,
                    format="%(asctime)s [%(levelname)s] %(message)s")

# Reutiliza o converter
converter = DocumentConverter()

# =========================================================
# HELPERS
# =========================================================


def build_repository_filename(doc_id: int, source_path: Path) -> Path:
    """
    Preserva o mesmo nome/versionamento do PDF.

    Exemplo:
    work_00060572#00063.pdf
    ->
    work_00060572#00063.md
    """

    id_str = str(doc_id).strip().zfill(8)

    parts = [
        id_str[0:2],
        id_str[2:4],
        id_str[4:6],
        id_str[6:8],
    ]

    md_name = source_path.with_suffix(".md").name

    return (BASE_DIR / "_repository" / parts[0] / parts[1] / parts[2] /
            parts[3] / md_name)


# =========================================================
# LIMPEZA DE TEXTO
# =========================================================


def normalize_unicode(text: str) -> str:
    """
    Corrige caracteres problemáticos comuns em PDFs.
    """

    replacements = {
        "\u00A0": " ",  # NBSP
        "\u00AD": "",  # Soft hyphen
        "\u200B": "",  # Zero-width space
        "\t": " ",
        "\r": "",
    }

    for old, new in replacements.items():
        text = text.replace(old, new)

    return text


def remove_excess_spaces(text: str) -> str:
    """
    Remove excesso de espaços e linhas vazias.
    """

    text = re.sub(r"[ ]{2,}", " ", text)

    text = re.sub(r"\n{3,}", "\n\n", text)

    return text.strip()


def rebuild_broken_lines(text: str) -> str:
    """
    Reconstrói linhas quebradas preservando markdown.
    """

    lines = text.splitlines()

    rebuilt = []

    current = ""

    for raw_line in lines:

        line = raw_line.strip()

        # --------------------------------------------
        # linha vazia
        # --------------------------------------------

        if not line:

            if current:
                rebuilt.append(current.strip())
                current = ""

            rebuilt.append("")

            continue

        # --------------------------------------------
        # títulos markdown
        # --------------------------------------------

        if line.startswith("#"):

            if current:
                rebuilt.append(current.strip())
                current = ""

            rebuilt.append(line)

            continue

        # --------------------------------------------
        # listas markdown
        # --------------------------------------------

        if (line.startswith("- ") or line.startswith("* ")
                or re.match(r"^\d+\.", line)):

            if current:
                rebuilt.append(current.strip())
                current = ""

            rebuilt.append(line)

            continue

        # --------------------------------------------
        # normaliza espaços
        # --------------------------------------------

        line = re.sub(r"\s+", " ", line)

        # --------------------------------------------
        # junta linhas pequenas
        # --------------------------------------------

        if current:

            short_line = len(line) < 60

            isolated_word = len(line.split()) <= 3

            upper_line = (line.upper() == line and len(line) < 40)

            if (short_line or isolated_word or upper_line):

                current += " " + line

                continue

        # --------------------------------------------
        # salva bloco anterior
        # --------------------------------------------

        if current:
            rebuilt.append(current.strip())

        current = line

    # último bloco
    if current:
        rebuilt.append(current.strip())

    final_text = "\n".join(rebuilt)

    # remove espaços antes de pontuação
    final_text = re.sub(r"\s+([,.;:!?])", r"\1", final_text)

    # hífen quebrado
    final_text = re.sub(r"-\s+", "-", final_text)

    # múltiplos espaços
    final_text = re.sub(r"[ ]{2,}", " ", final_text)

    return final_text


def clean_markdown(text: str) -> str:
    """
    Pipeline seguro de limpeza.
    """

    text = normalize_unicode(text)

    # normaliza espaços
    text = re.sub(r"[ \t]+", " ", text)

    # remove linhas absurdamente quebradas
    text = rebuild_broken_lines(text)

    # remove excesso
    text = remove_excess_spaces(text)

    return text


# =========================================================
# DOCLING
# =========================================================


def convert_pdf_to_markdown(source: Path) -> str:
    """
    Converte PDF usando Docling.
    """

    logging.info(f"Convertendo PDF: {source}")

    result = converter.convert(str(source))

    markdown = result.document.export_to_markdown()

    markdown = clean_markdown(markdown)

    return markdown


# =========================================================
# ARQUIVOS
# =========================================================


def save_markdown_file(path: Path, content: str) -> None:
    """
    Salva markdown UTF-8.
    """

    path.parent.mkdir(parents=True, exist_ok=True)

    path.write_text(content, encoding="utf-8")

    logging.info(f"Arquivo salvo: {path}")


def copy_to_repository(source_md: Path, repository_md: Path) -> None:
    """
    Copia markdown para repositório BRAPCI.
    """

    if not source_md.exists():

        raise FileNotFoundError(f"Arquivo origem inexistente: {source_md}")

    # resolve paths absolutos
    source_resolved = source_md.resolve()
    repo_resolved = repository_md.resolve()

    # evita copiar arquivo para ele mesmo
    if source_resolved == repo_resolved:

        logging.info("Arquivo já está no repositório final.")

        return

    repository_md.parent.mkdir(parents=True, exist_ok=True)

    shutil.copy2(source_md, repository_md)

    logging.info(f"Arquivo copiado para: {repository_md}")


# =========================================================
# PROCESSAMENTO PRINCIPAL
# =========================================================

def fileName(id):
    source_path = Path(source)
    repository_md = build_repository_filename(id, source_path)
    return repository_md

def save_file_docling(source: str,
                      doc_id: int,
                      force: bool = False) -> Optional[Path]:
    """
    Fluxo principal:
    - converte PDF
    - limpa markdown
    - salva markdown
    - copia para repositório
    """

    source_path = Path(source)

    if not source_path.exists():

        logging.error(f"Arquivo não encontrado: {source}")

        return None

    # markdown local
    generated_md = source_path.with_suffix(".md")

    # markdown no repositório
    repository_md = build_repository_filename(doc_id, source_path)

    # =====================================================
    # GERA MARKDOWN
    # =====================================================

    if generated_md.exists() and not force:

        logging.info(f"Markdown já existe: {generated_md}")

    else:

        try:

            markdown = convert_pdf_to_markdown(source_path)

            save_markdown_file(generated_md, markdown)

        except Exception as e:

            logging.exception(f"Erro na conversão do PDF: {e}")

            return None

    # =====================================================
    # COPIA PARA REPOSITÓRIO
    # =====================================================

    try:

        copy_to_repository(generated_md, repository_md)

    except Exception as e:

        logging.exception(f"Erro ao copiar para repositório: {e}")

        return None

    logging.info(f"Processamento concluído: {source}")

    return repository_md


# =========================================================
# COMPATIBILIDADE LEGADA
# =========================================================

saveFileD = save_file_docling

# =========================================================
# EXECUÇÃO
# =========================================================

if __name__ == "__main__":

    if len(sys.argv) < 3:

        print("Uso:\n"
              "python script.py arquivo.pdf 392175")

        sys.exit(1)

    try:

        pdf_file = sys.argv[1]

        doc_id = int(sys.argv[2])

    except ValueError:

        print("Erro: doc_id precisa ser numérico")

        sys.exit(1)

    result = save_file_docling(source=pdf_file, doc_id=doc_id)

    if result:

        print(f"\nMarkdown gerado em:\n{result}")

    else:

        print("\nFalha no processamento.")
