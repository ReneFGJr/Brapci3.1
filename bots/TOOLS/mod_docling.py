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


def build_repository_filename(doc_id: int) -> Path:
    """
    Gera caminho no formato:

    /data/Brapci3.1/public/_repository/00/39/21/75/work_00392175#00000.md
    """

    id_str = str(doc_id).strip().zfill(8)

    parts = [
        id_str[0:2],
        id_str[2:4],
        id_str[4:6],
        id_str[6:8],
    ]

    return (BASE_DIR / "_repository" / parts[0] / parts[1] / parts[2] /
            parts[3] / f"work_{id_str}#00000.md")


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
    Reconstrói textos quebrados por PDFs acadêmicos antigos.
    """

    lines = text.splitlines()

    rebuilt = []

    paragraph = ""

    for raw_line in lines:

        line = raw_line.strip()

        # ignora vazio
        if not line:

            if paragraph:
                rebuilt.append(paragraph.strip())
                paragraph = ""

            continue

        # remove múltiplos espaços unicode
        line = re.sub(r"\s+", " ", line)

        # preserva títulos markdown
        if line.startswith("#"):

            if paragraph:
                rebuilt.append(paragraph.strip())
                paragraph = ""

            rebuilt.append(line)

            continue

        # palavra isolada MAIÚSCULA
        if (len(line.split()) <= 3 and line.upper() == line
                and not line.endswith(".")):

            paragraph += " " + line

            continue

        # linha curta = continua
        if (paragraph and len(line) < 200
                and not re.match(r"^[A-Z][a-z]+:$", line)):

            paragraph += " " + line

        else:

            if paragraph:
                rebuilt.append(paragraph.strip())

            paragraph = line

    if paragraph:
        rebuilt.append(paragraph.strip())

    # limpa espaços extras
    final_text = "\n\n".join(rebuilt)

    # remove espaços antes de pontuação
    final_text = re.sub(r"\s+([,.;:!?])", r"\1", final_text)

    # corrige hífen quebrado
    final_text = re.sub(r"-\s+", "-", final_text)

    return final_text


def rebuild_broken_lines2(text: str) -> str:
    """
    Reconstrói linhas quebradas típicas de PDF em colunas.
    """

    lines = text.splitlines()

    rebuilt = []
    current = ""

    for line in lines:

        line = line.strip()

        # linha vazia
        if not line:

            if current:
                rebuilt.append(current.strip())
                current = ""

            continue

        # títulos markdown
        if line.startswith("#"):

            if current:
                rebuilt.append(current.strip())
                current = ""

            rebuilt.append(line)
            continue

        # continua parágrafo
        if (current and len(line) < 120 and not re.match(r"^[A-Z\s]+$", line)):

            current += " " + line

        else:

            if current:
                rebuilt.append(current.strip())

            current = line

    if current:
        rebuilt.append(current.strip())

    return "\n\n".join(rebuilt)


def clean_markdown(text: str) -> str:
    """
    Pipeline completo de limpeza.
    """

    text = normalize_unicode(text)

    text = rebuild_broken_lines(text)

    text = remove_excess_spaces(text)

    return text


def convert_pdf_to_markdown(source: Path) -> str:
    """
    Converte PDF usando Docling.
    """

    logging.info(f"Convertendo PDF: {source}")

    result = converter.convert(str(source))

    markdown = result.document.export_to_markdown()

    markdown = clean_markdown(markdown)

    return markdown


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

    repository_md.parent.mkdir(parents=True, exist_ok=True)

    shutil.copy2(source_md, repository_md)

    logging.info(f"Arquivo copiado para: {repository_md}")


# =========================================================
# PROCESSAMENTO PRINCIPAL
# =========================================================


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

    generated_md = source_path.with_suffix(".md")

    repository_md = build_repository_filename(doc_id)

    # -----------------------------------------------------
    # GERA MARKDOWN
    # -----------------------------------------------------

    if generated_md.exists() and not force:

        logging.info(f"Markdown já existe: {generated_md}")

    else:

        try:

            markdown = convert_pdf_to_markdown(source_path)

            save_markdown_file(generated_md, markdown)

        except Exception as e:

            logging.exception(f"Erro na conversão do PDF: {e}")

            return None

    # -----------------------------------------------------
    # COPIA PARA REPOSITÓRIO
    # -----------------------------------------------------

    try:

        copy_to_repository(generated_md, repository_md)

    except Exception as e:

        logging.exception(f"Erro ao copiar para repositório: {e}")

        return None

    logging.info(f"Processamento concluído: {source}")

    return repository_md


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
