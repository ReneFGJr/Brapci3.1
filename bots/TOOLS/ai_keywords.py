import re

def extract_keywords(text,id):
    text = text.replace(chr(13),' ')
    # Use regex to find "Palavras-chave:" followed by any text until the end of the line
    match = re.search(r"Palavras-chave:\s*(.*)", text)
    if match:
        keywords = match.group(1).split(";")  # Split keywords separated by semicolons
        return [keyword.strip() for keyword in keywords]  # Strip whitespace from each keyword
    else:
        return []  # Return an empty list if "Palavras-chave:" not found