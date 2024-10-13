import sys
import re

# Defining a function to calculate unique author frequencies
def calculate_author_frequencies(text, output_file):
    # Splitting the text by author names using delimiters like ';' and '\n'
    authors = re.split(r'[;\n]', text)

    # Cleaning and stripping extra spaces
    authors = [author.strip() for author in authors if author.strip()]

    # Counting the occurrences of each author
    author_count = Counter(authors)

    # Criar o arquivo .net para salvar o grafo
    with open(output_file, 'w', encoding='utf-8') as f_out:
        # Escrever os nós
        f_out.write(author_count)

if __name__ == "__main__":
    # Ensure the correct number of arguments is provided
    if len(sys.argv) != 2:
        print("Usage: python3 txt4net.py <input_file> <output_file>")
        sys.exit(1)

    # Parse the input and output file paths
    input_file = sys.argv[1]
    output_file = input_file.replace('.txt','__unit.txt')

    # Create the .net file
    calculate_author_frequencies(input_file, output_file)

    print({output_file})