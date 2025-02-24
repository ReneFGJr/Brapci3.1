import database
def nbr_author(n):
    n = n.replace("'","´")
    n2 = n
    if (n.find(';') > 0):
        p = n.find(';')
        n2 = n[0:p].strip()

    if (n2.find(',') > 0):
        p = n2.find(',')
        n2 = n2[p+1:].strip() + ' ' + n2[0:p].strip()
    n2.strip()
    return n2

def nbr_subject(n):
    n2 = n.lower()
    nA = n2[0:1].upper()
    n2 = nA+n2[1:]
    return n2

def nbr_title(n):
    n2 = n.lower()
    nA = n2[0:1].upper()
    n2 = nA+n2[1:]

    qr = "select * from brapci_vc.words "
    rows = database.query(qr)
    n2 = ' '+n2+' '

    for r in rows:
        print(r)
        n2 = n2.replace(' '+r[1]+' ',' '+r[2]+' ')
    n2 = n2.strip()
    return n2