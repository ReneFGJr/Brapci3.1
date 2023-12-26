def check(lg):
    if (lg == 'pt-BR'):
        lg = 'pt'
    elif (lg == 'pt-PT'):
        lg = 'pt'
    elif (lg == 'por'):
        lg = 'pt'
    elif (lg == 'pt'):
        lg = 'pt'
    elif (lg == 'en-US'):
        lg = 'en'
    elif (lg == 'eng'):
        lg = 'en'
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
    else:
        print("ERRO LANGUAGE ",lg)
        quit()
    return lg