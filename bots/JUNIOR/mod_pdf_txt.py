import fitz  # PyMuPDF

def pdf_to_txt(pdf_path, txt_path):
    # Open the PDF file
    pdf_document = fitz.open(pdf_path)

    # Open the output TXT file
    with open(txt_path, 'w', encoding='utf-8') as txt_file:
        # Iterate through each page
        for page_num in range(len(pdf_document)):
            page = pdf_document[page_num]
            blocks = page.get_text("blocks")
            for block in blocks:
                block_text = block[4]
                # Split the block text into lines
                lines = block_text.splitlines()
                for line in lines:
                    # Ensure a space after each word
                    words = line.split()
                    separated_line = ' '.join(words)
                    txt_file.write(separated_line + "\n")
                txt_file.write("\n")

    print(f"PDF has been converted to {txt_path}")
# Specify the input PDF and output TXT file paths
#pdf_path = 'txt/sample-enancib.pdf'
pdf_path = 'txt/sample-apa.pdf'
#pdf_path = 'txt/sample.pdf'
txt_path = 'txt/full_text.txt'

# Convert the PDF to TXT
pdf_to_txt(pdf_path, txt_path)
