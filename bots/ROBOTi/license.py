import re

def tipo(n):
    print("LLLLLLLLL",n)
    #if (n=='https://creativecommons.org/licenses/by/4.0'):
    #    return "CCBY4.0"

    http = re.findall("https?:\/\/(?:creativecommons.org)?[a-zA-Z0-9\/\.\-_\+]+",n)
    if (http != '') and (http != []):
        http = http[0]
        http = http.replace('https://creativecommons.org/licenses/','')
        if (http=='by/4.0'):
            http = 'CCBY4.0'
        return http

    other = re.findall('Copyright?[a-zA-Z0-9\/\.\-_\+]+',n)
    if (other != '') and (other != []):
        return "Copr"
    return "OO"