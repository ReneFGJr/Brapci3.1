from flask import Flask
#import mod_embening
import mod_api

app = Flask(__name__)

@app.route('/')
def hello_world():
    #return mod_embening.separa_silabas("Gabriel")
    sx = mod_api.test()
    return 'Hello, World-2!' + sx

if __name__ == '__main__':
    app.run()