import re
import sys
import mod_api

def extract_keywords(text,id):
    text = text.replace(chr(10),' ')
    text = text.replace('.',';')
    # Use regex to find "Palavras-chave:" followed by any text until the end of the line
    #match = re.search(r"Palavras-chave:\s*(.*)", text)
    match = re.search(r"Palavras-chave:\s*(.*?)(?=Abstract:)", text, re.DOTALL)

    if match:
        keywords = match.group(1).split(";")  # Split keywords separated by semicolons
        keys =  [keyword.strip().capitalize() for keyword in keywords if keyword.strip()]

        url = 'https://cip.brapci.inf.br/api/rdf/createConcept/Subject?name='
        for k in keys:
            if k != '':
                data = {'name':k}
                rst = mod_api.api_post(url+k,data)
                print(rst)

                try:
                    name = rst['Name']

                    if name != '':
                        idr = rst['id']
                        print("==>",rst)
                        print(rst['id'])
                        url = 'https://cip.brapci.inf.br/api/rdf/addData/?source=' + id + '&prop=hasSubject&resource=' + idr
                        print(url)
                except:
                    print("=================ERRO===========")
                    print(rst)

        return keys
    else:
        return []  # Return an empty list if "Palavras-chave:" not found