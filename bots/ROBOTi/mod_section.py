import mod_literal
import mod_class
import mod_concept
import mod_data

def register_literal(IDC,term,lg):
    IDliteral = mod_literal.register(term,lg)
    IDClass = mod_class.getClass('Subject')

    IDCt = mod_concept.register(IDClass,IDliteral)
    return mod_data.register(IDC,'hasSubject',IDCt)
