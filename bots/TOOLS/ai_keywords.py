import re

def extract_keywords(text,id):
    text = text.replace(chr(10),' ')
    text = text.replace('.',';')
    # Use regex to find "Palavras-chave:" followed by any text until the end of the line
    #match = re.search(r"Palavras-chave:\s*(.*)", text)
    match = re.search(r"Palavras-chave:\s*(.*?)(?=Abstract:)", text, re.DOTALL)
    print(match)
    if match:
        keywords = match.group(1).split(";")  # Split keywords separated by semicolons
        keys =  [keyword.strip().capitalize() for keyword in keywords]

        return keys
    else:
        return []  # Return an empty list if "Palavras-chave:" not found