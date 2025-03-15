from docling.document_converter import DocumentConverter
from pathlib import Path
import json
import sys

def saveFileD(source):
    #
    # source = "https://arxiv.org/pdf/2408.09869"  # document per local path or URL
    converter = DocumentConverter()
    result = converter.convert(source)
    txt = result.document.export_to_markdown()

    ## Export results
    doc_filename = source.replace('.pdf','.md')

    # Export Deep Search document JSON format:
    print("Exporting Deep Search document JSON format...")
    print(f"{doc_filename}.json")

    with (f"{doc_filename}.json").open("w", encoding="utf-8") as fp:
        #fp.write(json.dumps(txt))
        fp.write(txt)

saveFileD('/data/Brapci3.1/public/_repository/00/19/94/74/work_00199474#00000.pdf')