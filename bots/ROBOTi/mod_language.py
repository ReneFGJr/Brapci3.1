def detect(t):
    l = 'nn'
    if '@' in t:
        l = t[-2:]
        t = t[:-3]
    return [t,l]

def detect_language(text):
    portuguese_common_words = {'de', 'a', 'o', 'que', 'e', 'do', 'da', 'em', 'um', 'para'}
    english_common_words = {'the', 'be', 'to', 'of', 'and', 'a', 'in', 'that', 'have', 'I'}
    spanish_common_words = {'de', 'la', 'que', 'el', 'en', 'y', 'a', 'los', 'del', 'se'}
    french_common_words = {'de', 'la', 'et', 'les', 'des', 'en', 'un', 'une', 'est', 'que'}

    text_words = set(text.lower().split())

    languages = {
        'portuguese': len(text_words & portuguese_common_words),
        'english': len(text_words & english_common_words),
        'spanish': len(text_words & spanish_common_words),
        'french': len(text_words & french_common_words)
    }

    detected_language = max(languages, key=languages.get)

    return detected_language

def check(lg):
    if (lg == 'pt.'):
        lg = 'pt'
    if (lg == 'pt-BR'):
        lg = 'pt'
    elif (lg == 'pt-PT'):
        lg = 'pt'
    elif (lg == 'por'):
        lg = 'pt'
    elif (lg == 'pt'):
        lg = 'pt'
    elif (lg == 'en'):
        lg = 'en'
    elif (lg == 'en-US'):
        lg = 'en'
    elif (lg == 'eng'):
        lg = 'en'
    elif (lg == 'es.'):
        lg = 'es'
    elif (lg == 'es-ES'):
        lg = 'es'
    elif (lg == 'ca-ES'):
        lg = 'es'
    elif (lg == 'spa'):
        lg = 'es'
    elif (lg == 'es'):
        lg = 'es'
    elif (lg == 'fr-FR'):
        lg = 'fr'
    elif (lg == 'fr-CA'):
        lg = 'fr'
    elif (lg == 'fra'):
        lg = 'fr'
    elif (lg == 'cat'):
        lg = 'ct'
    elif (lg == 'it-IT'):
        lg = 'it'
    elif (lg == 'ita'):
        lg = 'it'
    elif (lg == 'de-DE'):
        lg = 'de'
    elif (lg == '0'):
        lg = 'nn'
    elif (lg == 'mul'):
        lg = 'nn'
    else:
        lg = 'pt'
        #print("ERRO LANGUAGE ",lg)
        #quit()
    return lg