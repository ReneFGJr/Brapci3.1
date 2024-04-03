import os
from google.cloud import translate_v2 as translate
credential_path = '../../../brapci-90fc0f16c494.json'
os.environ['GOOGLE_APPLICATION_CREDENTIALS'] = credential_path

translate_client = translate.Client()



text = "Os instrumentos de representação da informação nas bibliotecas públicas estaduais: um estudo exploratório no contexto brasileiro"
#target = 'pt'
target = 'en'

translation = translate_client.translate(
    text,
    target_language=target
)

print(u"Texto Traduzido: {}".format(translation['translatedText']))


#            $url = 'https://translation.googleapis.com/language/translate/v2';
#            $url .= '?q='.html_entity_decode($txt);
#            $url .= '&target='.$target;
#            $url .= '&source='.$ori;
#            $url .= '&key='.$_ENV['google_apikey_translate'];