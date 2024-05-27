from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route('/process', methods=['GET','POST'])
def process_data():
    # Verificar se o Content-Type é 'application/json'
    if request.content_type != 'application/json':
        return jsonify({'error': 'Content-Type must be application/json'}), 415

    # Extrair o dado da requisição JSON
    data = request.json.get('data')
    if not data:
        return jsonify({'error': 'No data provided'}), 400

    # Dividir a string de entrada em palavras
    words = data.split()

    return jsonify({'words': words})

if __name__ == '__main__':
    app.run(debug=True)