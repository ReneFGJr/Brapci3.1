from flask import Flask
import mod_embening

app = Flask(__name__)

@app.route('/')
def hello_world():
    #return mod_embening.separa_silabas("Gabriel")
    return 'Hello, World-2!'

if __name__ == '__main__':
    app.run()