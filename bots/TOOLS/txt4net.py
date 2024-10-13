import sys

def create_net_file_from_author_list(input_path, output_path):
    # Read the input file
    with open(input_path, 'r', encoding='utf-8') as file:
        content = file.read()

    # Split the content into different groups of authors
    groups = content.split("\n")

    # Create a unique list of authors (nodes)
    authors = set()
    edges = []  # To track edges between authors

    for group in groups:
        author_list = group.split(";")
        clean_authors = [author.strip() for author in author_list if author.strip()]
        if len(clean_authors) > 1:  # Only process groups with more than one author
            for i in range(len(clean_authors)):
                for j in range(i + 1, len(clean_authors)):
                    edges.append((clean_authors[i], clean_authors[j]))
                    authors.update([clean_authors[i], clean_authors[j]])

    # Assign a unique ID to each author
    author_to_id = {author: idx + 1 for idx, author in enumerate(authors)}

    # Write the .net file
    with open(output_path, 'w', encoding='utf-8') as net_file:
        # Write vertices
        net_file.write(f"*Vertices {len(authors)}\n")
        for author, author_id in author_to_id.items():
            net_file.write(f"{author_id} \"{author}\"\n")

        # Write edges and include the number of edges
        net_file.write(f"*Edges {len(edges)}\n")
        for author1, author2 in edges:
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
