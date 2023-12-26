def check(lg):
    if (lg == 'pt-BR'):
        lg = 'pt'
    if (lg == 'pt-PT'):
        lg = 'pt'
    if (lg == 'por'):
        lg = 'pt'
    if (lg == 'pt'):
        lg = 'pt'
    elif (lg == 'en-US'):
        lg = 'en'
    elif (lg == 'es-ES'):
        lg = 'es'
    else:
        print("ERRO LANGUAGE ",lg)
        quit()
    return lg