import re

def recognizer(n):
    x = ''

    http = re.findall("https?:\/\/(?:www\.)?[a-zA-Z0-9\/\.\-_\+]+",n)

    if (http != '') and (http != []):
        return dict(type='HTTP',value=http)

    doi = re.findall("10.?[a-zA-Z0-9\/\.\-_\+]+",n)
    if (doi != '') and (doi != []):
        return dict(type='DOI',value=doi)
    return x
