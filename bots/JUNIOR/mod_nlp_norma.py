import re

def identify_format(reference):
    # Patterns for APA
    apa_pattern = re.compile(r"\(\d{4}\)\.")

    # Patterns for ABNT
    abnt_pattern = re.compile(r"[A-Z]{2,}, [A-Z][a-z]+")

    # Patterns for Vancouver
    vancouver_pattern = re.compile(r"\d{4};\d+\(\d+\):\d+-\d+")

    if apa_pattern.search(reference):
        return "APA"
    elif abnt_pattern.search(reference):
        return "ABNT"
    elif vancouver_pattern.search(reference):
        return "Vancouver"
    else:
        return "Unknown"

def identify_all_references(text):
    # Split the text by new lines and filter out empty lines
    references = [ref.strip() for ref in text.split('\n') if ref.strip()]
    format_counts = {"APA": 0, "ABNT": 0, "Vancouver": 0, "Unknown": 0}
    total_references = len(references)

    for ref in references:
        format_type = identify_format(ref)
        format_counts[format_type] += 1

    # Calculate percentages
    format_percentages = {key: (value / total_references) * 100 for key, value in format_counts.items()}

    return format_percentages

# Load the content from the provided file
file_path = 'txt/references.txt'

with open(file_path, 'r', encoding='utf-8') as file:
    content = file.read()

# Identify all references and calculate percentages
results = identify_all_references(content)

# Print results
for format_type, percentage in results.items():
    print(f"Format: {format_type}, Percentage: {percentage:.2f}%")
