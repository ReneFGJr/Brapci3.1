from PyPDF2 import PdfReader

def extract_references_text_to_file(pdf_path, output_file):
    reader = PdfReader(pdf_path)
    text = ""
    for page in reader.pages:
        text += page.extract_text()

    references_text = ""
    if 'REFERÊNCIAS' in text:
        references_section = text.split('REFERÊNCIAS')[-1]
        references_text = references_section

    with open(output_file, 'w', encoding='utf-8') as file:
        file.write(references_text)
    return output_file

# Extract references from the uploaded PDF and write to a text file
pdf_path = 'txt/sample-apa.pdf'
output_file = 'txt/references.txt'
extract_references_text_to_file(pdf_path, output_file)
