import re
import mod_literal
import mod_class
import mod_concept
import mod_data

def register_literal(IDC,term):
    term = nbr_licence(term)

    IDliteral = mod_literal.register(term,'nn')
    IDClass = mod_class.getClass('License')

    IDCt = mod_concept.register(IDClass,IDliteral)
    return mod_data.register(IDC,'hasLicense',IDCt)

def nbr_licence(T):
    if T == 'Copr':
        T = 'Copyright (c)'
    elif T == 'Copyright':
        T = 'Copyright (c)'
    else:
        try:
            T = T.upper()
        except:
            T = "RESERVERD"
    return T

def tipo(n):
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

    #print("Licença não localizada ",n)
    return "RESERVED"