from docling.document_converter import DocumentConverter
from pathlib import Path
import shutil
import json
import sys

def saveFileD(source):
    # Ensure source is a Path object
    source_path = Path(source)

    if not source_path.exists():
        print(f"Error: File '{source}' does not exist.")
        return

    # Create output filename
    doc_filename = source_path.with_suffix('.md')
    md_filename = Path(fileName(id))

    if doc_filename.exists():
        print(f"     Arquivo ja existe '{doc_filename}'")
    else:
        print(f"     Gerando arquivo '{doc_filename}'")

        # Initialize converter
        converter = DocumentConverter()

        try:
            result = converter.convert(str(source_path))
            txt = result.document.export_to_markdown()

            # Export Markdown file
            print(f"Exporting Deep Search document JSON format to {doc_filename}")

            with doc_filename.open("w", encoding="utf-8") as fp:
                fp.write(txt)

            print("Export successful!")

        except Exception as e:
            print(f"Error during conversion: {e}")

    # Cria o arquivo de saída Markdown no ID
    if not md_filename.exists():
        md_filename.parent.mkdir(parents=True, exist_ok=True)
        shutil.copyfile(doc_filename, md_filename)
        print(f"     Criado arquivo '{md_filename}'")


def fileName(id):
    dirT = '/data/Brapci3.1/public/'
    id_str = str(id).strip().zfill(8)

    # Ex.: 00392175 -> 00/39/21/75
    p1 = id_str[0:2]
    p2 = id_str[2:4]
    p3 = id_str[4:6]
    p4 = id_str[6:8]

    return f"{dirT}_repository/{p1}/{p2}/{p3}/{p4}/work_{id_str}#00000.md"
