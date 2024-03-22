import mod_literal
import mod_class
import mod_concept
import mod_data
import database

def register_literal(IDC,term,lg):

    IDliteral = mod_literal.register(term,lg)
    IDClass = mod_class.getClass('Subject')

    IDCt = mod_concept.register(IDClass,IDliteral)
    return mod_data.register(IDC,'hasSubject',IDCt)

def check_duplicate():
    print("Check Duplicate - Subject")
    IDClass = mod_class.getClass("Subject")

    qr = "select id_cc, cc_use, n_name, n_lang  "
    qr += " from brapci_rdf.rdf_concept "
    qr += " inner join brapci_rdf.rdf_literal ON id_n = cc_pref_term"
    qr += f" where cc_class = {IDClass}"
    qr += " and id_cc = cc_use "
    qr += " order by n_name, id_cc"

    row = database.query(qr)
    lastName = 'x'
    lastLang = 'x'
    for reg in row:
        name = reg[2]
        name = name.replace('-',' ')
        lang = reg[3]

        IDn1 = reg[0]

        if ((lang == lastLang) and (name == lastName) and (name != '::Em Branco::') and (name != '(empty)')):
            print(name,'|',lastName)
            mod_data.remicive(IDn1,IDn2)
        else:
            reg2 = reg
            lastName = name
            lastLang = lang
            IDn2 = IDn1


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