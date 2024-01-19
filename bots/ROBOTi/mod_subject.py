import mod_literal
import mod_class
import mod_concept
import mod_data

def register_literal(IDC,term,lg):

    IDliteral = mod_literal.register(term,lg)
    IDClass = mod_class.getClass('Subject')

    IDCt = mod_concept.register(IDClass,IDliteral)
    return mod_data.register(IDC,'hasSubject',IDCt)

def prepare(T):
    TR = []

    for i in range(len(T)):
        TE = T[i]
        lg = ''
        if '@' in TE:
            lg = TE[-2:]
            TE = TE[0:-3]

        nt = False
        if '. ' in TE:
            TE = TE.split('. ')
            for ix in range(len(TE)):
                TEe = TE[ix]
                TR.append([TEe,lg])
                nt = True
        if ';' in TE:
            TE = TE.split(';')
            for ix in range(len(TE)):
                TEe = TE[ix]
                TR.append([TEe,lg])
                nt = True
        if ':' in TE:
            TE = TE.split(':')
            for ix in range(len(TE)):
                TEe = TE[ix]
                TR.append([TEe,lg])
                nt = True

        if nt==False:
                TR.append([TE,lg])

    ####################### Normalize
    for i in range(len(TR)):
        T = TR[i][0]
        TR[i][0] = nbr_subject(T)

    return TR


    quit()

def nbr_subject(T):
    T = T.lower()
    M = T[0].upper()
    T = M + T[1:]
    return T

def register(T):
    print("Termos",T)
    quit()