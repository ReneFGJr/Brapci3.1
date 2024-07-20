import re
import os

def process_references(text):
    lines = text.split('\n')
    processed_lines = []
    buffer = ""

    for line in lines:
        if re.search(r'\(\d{4}\)', line):
            if buffer:
                processed_lines.append(buffer.strip())
            buffer = line.strip()
        else:
            buffer += " " + line.strip()

    if buffer:
        processed_lines.append(buffer.strip())

    return "\n".join(processed_lines)

# Load the content from the provided file
file_path = 'txt/references.txt'

with open(file_path, 'r', encoding='utf-8') as file:
    content = file.read()

# Process the references
processed_content = process_references(content)

# Define the output directory and file path
output_directory = 'txt'
output_file_path = os.path.join(output_directory, 'references_apa.txt')

# Create the directory if it doesn't exist
os.makedirs(output_directory, exist_ok=True)

# Save the processed content to the new file
with open(output_file_path, 'w', encoding='utf-8') as file:
    file.write(processed_content)

print(f"Processed content saved to {output_file_path}")
