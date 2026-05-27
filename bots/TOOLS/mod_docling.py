from pathlib import Path
from docling.document_converter import DocumentConverter
import shutil
import re
import logging

# =========================================================
# CONFIG
# =========================================================

BASE_DIR = Path("/data/Brapci3.1/public")
converter = DocumentConverter()

logging.basicConfig(level=logging.INFO,
                    format="%(asctime)s [%(levelname)s] %(message)s")

# =========================================================
# HELPERS
# =========================================================


def build_output_filename(doc_id: int) -> Path:
    """
    Gera o caminho:
    00/39/21/75/work_00392175#00000.md
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


def normalize_markdown(text: str) -> str:
    """
    Corrige problemas comuns do Docling/PDF:
    - espaços Unicode
    - soft hyphen
    - múltiplos espaços
    - quebras excessivas
    """

    # Espaço unicode
    text = text.replace("\u00A0", " ")

    # Soft hyphen
    text = text.replace("\u00AD", "")

    # Tabs
    text = text.replace("\t", " ")

    # Espaços múltiplos
    text = re.sub(r"[ ]{2,}", " ", text)

    # Muitas linhas vazias
    text = re.sub(r"\n{3,}", "\n\n", text)

    return text.strip()


def export_markdown(source: Path) -> str:
    """
    Converte PDF usando Docling.
    """

    result = converter.convert(str(source))

    markdown = result.document.export_to_markdown()

    return normalize_markdown(markdown)


# =========================================================
# MAIN
# =========================================================


def save_file_docling(source: str, doc_id: int) -> None:

    source_path = Path(source)

    if not source_path.exists():
        logging.error(f"Arquivo não encontrado: {source}")
        return

    generated_md = source_path.with_suffix(".md")
    repository_md = build_output_filename(doc_id)

    # -----------------------------------------------------
    # Gera markdown
    # -----------------------------------------------------

    if not generated_md.exists():

        logging.info(f"Gerando markdown: {generated_md}")

        try:

            markdown = export_markdown(source_path)

            generated_md.write_text(markdown, encoding="utf-8")

            logging.info("Markdown gerado com sucesso")

        except Exception as e:
            logging.exception(f"Erro na conversão: {e}")
            return

    else:
        logging.info(f"Markdown já existe: {generated_md}")

    # -----------------------------------------------------
    # Copia para repositório
    # -----------------------------------------------------

    try:

        repository_md.parent.mkdir(parents=True, exist_ok=True)

        shutil.copy2(generated_md, repository_md)

        logging.info(f"Arquivo copiado para: {repository_md}")

    except Exception as e:
        logging.exception(f"Erro ao copiar markdown: {e}")
