import re

def readTxt(filename="txt/full_text.txt"):
    try:
        with open(filename, 'r', encoding='utf-8') as file:
            content = file.readlines()
        return content
    except FileNotFoundError:
        return "Arquivo não encontrado."
    except Exception as e:
        return f"Ocorreu um erro: {e}"

def clearNumTxt(lines):
    # Remove números de cada linha e junta tudo em uma string
    cleaned_string = re.sub(r'\d+', '', lines)
    return cleaned_string

def processText(filename="txt/full_text.txt"):
    lines = readTxt(filename)
    if isinstance(lines, str):
        return lines  # Retorna a mensagem de erro se houver

    processed_lines = []
    for i, line in enumerate(lines, start=1):
        removed = False
        original_line = line.strip()
        cleaned_line = clearNumTxt(original_line)
        processed_lines.append((i, original_line, cleaned_line, removed))

    return processed_lines

def markDuplicates(processed_lines):
    seen_lines = {}
    for i, (line_num, original_line, cleaned_line, removed) in enumerate(processed_lines):
        if (cleaned_line != '') and (len(cleaned_line) > 5):
            if cleaned_line in seen_lines:
                # Marcar a linha atual como duplicada
                processed_lines[i] = (line_num, original_line, cleaned_line, True)
                # Marcar a primeira ocorrência como duplicada
                first_occurrence_index = seen_lines[cleaned_line]
                processed_lines[first_occurrence_index] = (processed_lines[first_occurrence_index][0],
                                                           processed_lines[first_occurrence_index][1],
                                                           processed_lines[first_occurrence_index][2], True)
            else:
                seen_lines[cleaned_line] = i
    return processed_lines

def getDuplicates(processed_lines):
    duplicates = [line for line in processed_lines if line[3] == True]
    return duplicates

def saveProcessedText(processed_lines, filename="txt/full_text_processed.txt"):
    try:
        with open(filename, 'w', encoding='utf-8') as file:
            for line_num, original_line, cleaned_line, removed in processed_lines:
                if removed:
                    #file.write("[REMOVED]\n")
                    file.write("\n")
                else:
                    file.write(f"{original_line}\n")
    except Exception as e:
        print(f"Ocorreu um erro ao salvar o arquivo: {e}")

def cleanProcessedFile(input_filename="txt/full_text_processed.txt", output_filename="txt/full_text_cleaned.txt"):
    def removeConsecutiveNewlines(text):
         return re.sub(r'\n{3,}', '\n', text)

    try:
        with open(input_filename, 'r', encoding='utf-8') as file:
            content = file.read()

        cleaned_content = removeConsecutiveNewlines(content)

        with open(output_filename, 'w', encoding='utf-8') as file:
            file.write(cleaned_content)

    except FileNotFoundError:
        print("Arquivo não encontrado.")
    except Exception as e:
        print(f"Ocorreu um erro: {e}")

def removeNF(input_file, output_file):
    with open(input_file, 'r', encoding='utf-8') as file:
        lines = file.readlines()

    new_lines = []
    for i in range(len(lines)):
        if i > 0 and re.match(r'^[a-záéíóúâêîôûãõç()\[\]{},;.]', lines[i]):
            new_lines[-1] = new_lines[-1].rstrip('\n') + ' ' + lines[i].lstrip()
        else:
            new_lines.append(lines[i])

    with open(output_file, 'w', encoding='utf-8') as file:
        file.writelines(new_lines)

# Exemplo de uso
file = 'txt/full_text.txt'
file2 = 'txt/full_text_processed.txt'
file3 = 'txt/full_text_cleaned.txt'
file4 = 'txt/full_text_nf.txt'
result = processText(file)
result = markDuplicates(result)
saveProcessedText(result,file2)
cleanProcessedFile(file2,file3)
removeNF(file3, file4)
print("Arquivo limpo e salvo em ",file4)