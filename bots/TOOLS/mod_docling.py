from docling.document_converter import DocumentConverter
from pathlib import Path
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
    if doc_filename.exists():
        print(f"Error: File '{doc_filename}' already exists.")
        return

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

# Example usage

saveFileD('/data/Brapci3.1/public/_repository/00/19/94/74/work_00199474#00000.pdf')
