from flask import Flask, jsonify, send_file, render_template
import os,sys
import mod_fundos
import mod_photo

def main(argv: list):

    app = Flask(__name__)

    ######################################### /
    @app.route("/")
    def index():
        html = render_template("header.html")
        html += "<h1>API de Imagens</h1>"
        data = mod_fundos.getFundos()
        html += render_template("fundos/fundos_row.html", data=data)
        return html + "API de Imagens"


    @app.route("/imageThumb/<path:ID>", methods=["GET"])
    def get_thumb_image(ID):
        data = mod_photo.getID(ID)
        image_path = data[11] + "/thumbnails/thumb_" + data[1]
        try:
            return send_file(image_path, mimetype='image/jpeg')
        except FileNotFoundError:
            return {"error": "Imagem não encontrada"}, 404


    @app.route("/imageView/<path:ID>", methods=["GET"])
    def get_view_image(ID):
        data = mod_photo.getID(ID)
        image_path = data[11] + "/" + data[1]
        try:
            return send_file(image_path, mimetype='image/jpeg')
        except FileNotFoundError:
            return {"error": "Imagem não encontrada"}, 404

    @app.route("/image/<path:ID>", methods=["GET"])
    def get_images(ID):
        data = mod_photo.getID(ID)

        html = render_template("header.html")
        html += render_template("image/image_show.html", data=data)
        return html
        #return jsonify(data)

    @app.route("/fundo/<path:fundo>", methods=["GET"])
    def get_fundos(fundo):
        data = mod_fundos.getFundo(fundo)

        html = render_template("header.html")
        html += "<h1>Fundo</h1>"
        html += render_template("fundos/fundos_show.html", data=data)
        return html
        #return jsonify(data)

    @app.route("/metadata", methods=["GET"])
    def get_metadata():
        return jsonify(data)

    @app.route("/image", methods=["GET"])
    def get_image():
        image_path = os.path.join(data["Fundo"], data["Nome"])
        if os.path.exists(image_path):
            return send_file(image_path, mimetype='image/jpeg')
        return jsonify({"error": "Imagem não encontrada"}), 404

    app.run(debug=True)