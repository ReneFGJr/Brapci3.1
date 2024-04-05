import database
import mod_data
import mod_class
import mod_literal
import mod_GoogleTranslate

def process(ID):
    prop = mod_class.getClass("hasAbstract")
    qr = "select n_name, n_lang from brapci_rdf.rdf_data "
    qr += "inner join brapci_rdf.rdf_literal ON d_literal = id_n"
    qr += f" where d_r1 = {ID} and d_p = {prop}"
    row = database.query(qr)

    pt = False
    en = False
    es = False

    for item in row:
        lg = item[1]
        txt = item[0]
        if (lg == 'pt'):
            pt = True
            termPT = txt
        elif (lg == 'en'):
            en = True
            termEN = txt
        elif (lg == 'es'):
            es = True
            termES = txt

    if (pt == True) or (en == True) or (es == True):
        if (pt) and (not en):
            print("Traduzindo ABSTRACT do Portugues para o Inglês")
            termEN = mod_GoogleTranslate.translate(termPT,'en')
            IDl = mod_literal.register(termEN,'en')
            mod_data.register(ID,"hasAbstract",0,IDl)

        if (pt) and (not es):
            print("Traduzindo ABSTRACT do Portugues para o Espanhol")
            termEN = mod_GoogleTranslate.translate(termPT,'es')
            IDl = mod_literal.register(termEN,'es')
            mod_data.register(ID,"hasAbstract",0,IDl)