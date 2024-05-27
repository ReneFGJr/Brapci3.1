from flask import Flask, request, jsonify
#import mod_embening
import mod_api

app = Flask(__name__)

@app.route('/')
def hello_world():
    #return mod_embening.separa_silabas("Gabriel")
    sx = mod_api.test()
    data = request.json.get('data')
    if not data:
        return jsonify({'error': 'No data provided'}), 400

    # Dividir a string de entrada em palavras
    words = data.split()

    return jsonify({'words': words})

if __name__ == '__main__':
    app.run()