import mod_translate_title
import mod_translate_abstract
import mod_PDF
import sys

print("TRADUTOR 1.0")

if (len(sys.argv) > 1):
    parm = sys.argv
    ID = parm[1]

    mod_translate_title.process(ID)
    mod_translate_abstract.process(ID)
    mod_PDF.convert(ID)