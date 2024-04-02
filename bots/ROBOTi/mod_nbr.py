def nbr_author(n):
    n2 = n
    if (n.find(';')):
        p = n.find(';')
        n2 = n[0:p].strip()
    print('===>',n2)

    if (n2.find(',')):
        p = n2.find(',')
        print("Virgula",p)
        n2 = n2[p+1:].strip() + ' ' + n2[0:p].strip()
    n2.strip()
    print('===>',n2)
    return n2

def nbr_subject(n):
    n2 = n.lower()
    nA = n2[0:1].upper()
    n2 = nA+n2[1:]
    return n2