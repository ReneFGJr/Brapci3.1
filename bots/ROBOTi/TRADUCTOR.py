import mod_translate_title
import mod_translate_abstract
import mod_PDF

print("TRADUTOR 1.0")
ID = 257789

mod_translate_title.process(ID)
mod_translate_abstract.process(ID)
mod_PDF.convert(ID)