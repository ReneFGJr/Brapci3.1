import os
from google.cloud import translate_v2 as translate
credential_path = '../../.Google.json'
os.environ['GOOGLE_APPLICATION_CREDENTIALS'] = credential_path
translate_client = translate.Client()

def translate(text,target='en'):

    translation = translate_client.translate(
        text,
        target_language=target
    )
    return translation['translatedText']
