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
    elif (lg == 'spa'):
        lg = 'es'
    elif (lg == 'fr-FR'):
        lg = 'fr'
    else:
        print("ERRO LANGUAGE ",lg)
        quit()
    return lg