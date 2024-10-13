import sys
import re
from collections import Counter

# Defining a function to calculate unique author frequencies
def calculate_author_frequencies(input_file, output_file):
    with open(input_file, 'r', encoding='utf-8') as f_in:
        text = f_in.read()

    text = text.replace('AUTHORS','')
    text = text.replace('Authors','')

    # Splitting the text by author names using delimiters like ';' and '\n'
    authors = re.split(r'[;\n]', text)

    # Cleaning and stripping extra spaces
    authors = [author.strip() for author in authors if author.strip()]

    # Counting the occurrences of each author
    author_count = Counter(authors)

    # Sorting the authors alphabetically
    sorted_authors = sorted(author_count.items())

    # Writing the result to the output file
    with open(output_file, 'w', encoding='utf-8') as f_out:
        # Write the frequencies in alphabetical order
        f_out.write(f"TERMO;TOTAL\n")
        for author, count in sorted_authors:
            f_out.write(f"{author};{count}\n")

if __name__ == "__main__":
    # Ensure the correct number of arguments is provided
    if len(sys.argv) != 2:
        print("Usage: python3 txt4net.py <input_file> <output_file>")
        sys.exit(1)

    # Parse the input and output file paths
    input_file = sys.argv[1]
    output_file = input_file.replace('.txt','.csv')

    # Create the .net file
    calculate_author_frequencies(input_file, output_file)

    print({output_file})