#pip install gTTS pyttsx3
import pyttsx3

def gerar_voz_pyttsx3(texto, velocidade=150):
    """
    Converte texto em voz offline usando pyttsx3.

    Args:
        texto (str): Texto a ser falado.
        velocidade (int): Velocidade da fala (padrão: 150).
    """
    try:
        engine = pyttsx3.init()
        engine.setProperty('rate', velocidade)  # Ajuste a velocidade
        engine.setProperty('voice', 'pt-br')  # Configura voz em português (se disponível)
        engine.say(texto)
        engine.runAndWait()
    except Exception as e:
        print(f"Erro: {e}")

# Exemplo de uso:
gerar_voz_pyttsx3("Olá mundo! Esta é uma voz gerada offline em Python.")