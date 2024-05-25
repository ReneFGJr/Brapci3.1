diretorio = '/data/Brapci3.1/bots/ROBOTi'
os.chdir(diretorio)

import mod_translate_title
import mod_translate_abstract
import mod_PDF
import sys
import os

print("TRADUTOR 1.1")

if (len(sys.argv) > 1):
    parm = sys.argv
    ID = parm[1]

    mod_translate_title.process(ID)
    mod_translate_abstract.process(ID)
    mod_PDF.convert(ID)
else:
    print("Without parameters")
    print("ex: TRADUCTOR 2343")