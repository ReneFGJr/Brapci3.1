def extract_references(input_file_path, output_file_path, output_file_name="cited.txt"):
    """
    Extracts the references section and everything that comes after it from the input file and saves it to a new file.

    Parameters:
    input_file_path (str): Path to the input file.
    output_directory (str): Directory where the output file will be saved.
    output_file_name (str): Name of the output file. Default is "cited.txt".

    Returns:
    str: Path to the output file.
    """
    # Read the content of the file
    with open(input_file_path, 'r', encoding='utf-8') as file:
        lines = file.readlines()

    # Find the starting point of the "REFERÊNCIAS" section
    start_index = None
    for i, line in enumerate(lines):
        if "REFERÊNCIAS" in line:
            start_index = i
            break

    # Extract the content starting from the references section
    if start_index is not None:
        references_content = lines[start_index:]
    else:
        references_content = []


    # Write the extracted content to the new file
    with open(output_file_path, 'w', encoding='utf-8') as output_file:
        output_file.writelines(references_content)

    return output_file_path

# Using the function with the provided file
input_file_path = 'txt/full_text_nf.txt'
output_file_path = 'txt/full_cited_nf'
extract_references(input_file_path, output_file_path)
