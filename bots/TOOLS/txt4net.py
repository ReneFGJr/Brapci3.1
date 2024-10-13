import sys

def create_net_file_from_author_list(input_path, output_path):
    # Read the input file
    with open(input_path, 'r', encoding='utf-8') as file:
        content = file.read()

    # Split the content into different groups of authors
    groups = content.split("\n")

    # Create a unique list of authors (nodes)
    authors = set()
    for group in groups:
        author_list = group.split(";")
        for author in author_list:
            author = author.strip()
            if author:  # Ensure author is not an empty string
                authors.add(author)

    # Assign a unique ID to each author
    author_to_id = {author: idx + 1 for idx, author in enumerate(authors)}

    # Write the .net file
    with open(output_path, 'w', encoding='utf-8') as net_file:
        # Write vertices
        net_file.write(f"*Vertices {len(authors)}\n")
        for author, author_id in author_to_id.items():
            net_file.write(f"{author_id} \"{author}\"\n")

        # Write edges
        net_file.write("*Edges\n")
        for group in groups:
            author_list = [author.strip() for author in group.split(";") if author.strip()]
            if len(author_list) > 1:  # Only process groups with more than one author
                for i in range(len(author_list)):
                    for j in range(i + 1, len(author_list)):
                        author1 = author_list[i]
                        author2 = author_list[j]
                        if author1 in author_to_id and author2 in author_to_id:
                            net_file.write(f"{author_to_id[author1]} {author_to_id[author2]}\n")

if __name__ == "__main__":
    # Ensure the correct number of arguments is provided
    if len(sys.argv) != 2:
        print("Usage: python3 txt4net.py <input_file> <output_file>")
        sys.exit(1)

    # Parse the input and output file paths
    input_file = sys.argv[1]
    output_file = input_file + '.net'

    # Create the .net file
    create_net_file_from_author_list(input_file, output_file)

    print({output_file})
