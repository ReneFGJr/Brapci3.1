import mod_literal
import mod_class
import mod_concept
import mod_data

def register_literal(IDC,name):
    name = nbr_author(name)

    IDliteral = mod_literal.register(name,'nn')
    IDClass = mod_class.getClass('Person')

    IDCt = mod_concept.register(IDClass,IDliteral)
    return mod_data.register(IDC,'hasAuthor',IDCt)

def nbr_author(n):
    if ',' in n:
        print("NOME COM VIRGULA",n)
        print(n)
        quit()

    nm = n.lower()
    nm = nm.split(' ')

    pre = ['de','da','e','do']

    n = ''
    for i in range(len(nm)):
        na = nm[i]
        na1 = na[:1]
        na2 = na[1:]

        for x in range(len(pre)):
            if na == pre[x]:
                na2 = na
                na1 = ''
        if n != '':
            n += ' '
        n += na1.upper()+na2
    print(n)
    return n
