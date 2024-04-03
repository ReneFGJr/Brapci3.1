import os
from google.cloud import translate_v2 as translate
credential_path = '../../.Google.json'
os.environ['GOOGLE_APPLICATION_CREDENTIALS'] = credential_path

def translate(text,target='en'):
    translate_client = translate.Client()
    translation = translate_client.translate(
        text,
        target_language=target
    )
    return translation['translatedText']
