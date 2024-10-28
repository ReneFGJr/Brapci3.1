import re

def extract_keywords(text,id):
    text = text.replace(chr(10),' ')
    text = text.replace('.',';')
    # Use regex to find "Palavras-chave:" followed by any text until the end of the line
    #match = re.search(r"Palavras-chave:\s*(.*)", text)
    match = re.search(r"Palavras-chave:\s*(.*?)(?=Abstract:)", text, re.DOTALL)
    if match:
        keywords = match.group(1).split(";")  # Split keywords separated by semicolons
        return [keyword.strip().capitalize() for keyword in keywords]
    else:
        return []  # Return an empty list if "Palavras-chave:" not found