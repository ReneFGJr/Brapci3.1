import cv2
import mysql.connector
import numpy as np

# Configurações do Banco de Dados
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'PHOTO'
}

# Carregar modelo YOLO pré-treinado
net = cv2.dnn.readNet("yolov3.weights", "yolov3.cfg")
classes = []
with open("coco.names", "r") as f:
    classes = [line.strip() for line in f.readlines()]

layer_names = net.getLayerNames()
output_layers_indices = net.getUnconnectedOutLayers().flatten()
output_layers = [layer_names[i] for i in output_layers_indices]

def process_image(image_path):
    # Carregar imagem
    img = cv2.imread(image_path)
    height, width, channels = img.shape

    # Detecção de objetos
    blob = cv2.dnn.blobFromImage(img, 0.00392, (416, 416), (0, 0, 0), True, crop=False)
    net.setInput(blob)
    outs = net.forward(output_layers)

    # Processar detecções
    class_ids = []
    confidences = []
    boxes = []
    pessoas_count = 0

    for out in outs:
        for detection in out:
            scores = detection[5:]
            class_id = np.argmax(scores)
            confidence = scores[class_id]
            if confidence > 0.5:  # Threshold de confiança
                # Coordenadas da bounding box
                center_x = int(detection[0] * width)
                center_y = int(detection[1] * height)
                w = int(detection[2] * width)
                h = int(detection[3] * height)

                # Ponto superior esquerdo
                x = int(center_x - w / 2)
                y = int(center_y - h / 2)

                boxes.append([x, y, w, h])
                confidences.append(float(confidence))
                class_ids.append(class_id)

    # Aplicar Non-Maximum Suppression
    indexes = cv2.dnn.NMSBoxes(boxes, confidences, 0.5, 0.4)

    # Preparar dados para inserção no banco
    objects_data = []
    pessoa_count = 1

    for i in range(len(boxes)):
        if i in indexes:
            label = str(classes[class_ids[i]])
            if label == 'person':
                object_label = f"Pessoa_{pessoa_count}"
                pessoa_count += 1
            else:
                object_label = label

            x, y, w, h = boxes[i]
            objects_data.append((
                image_path.split('/')[-1],  # Nome do arquivo
                object_label,
                'person' if label == 'person' else 'object',
                f"{x},{y},{x+w},{y+h}",
                confidences[i]
            ))

    return objects_data

def setup_database():
    conn = mysql.connector.connect(
        host=DB_CONFIG['host'],
        user=DB_CONFIG['user'],
        password=DB_CONFIG['password']
    )
    cursor = conn.cursor()

    cursor.execute(f"CREATE DATABASE IF NOT EXISTS {DB_CONFIG['database']}")
    cursor.execute(f"USE {DB_CONFIG['database']}")

    cursor.execute('''CREATE TABLE IF NOT EXISTS detected_objects (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        image_name VARCHAR(255),
                        object_id VARCHAR(50),
                        object_type ENUM('person', 'object'),
                        bounding_box VARCHAR(50),
                        confidence FLOAT,
                        INDEX(image_name),
                        INDEX(object_id)
                    )''')
    conn.commit()
    cursor.close()
    conn.close()

def save_to_database(data):
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()

    insert_query = '''INSERT INTO detected_objects
                      (image_name, object_id, object_type, bounding_box, confidence)
                      VALUES (%s, %s, %s, %s, %s)'''

    cursor.executemany(insert_query, data)
    conn.commit()
    cursor.close()
    conn.close()

if __name__ == "__main__":
    image_path = "exemplo.jpg"  # Substituir pelo caminho da sua imagem
    image_path = r"E:\Projeto\www\Brapci3.1\bots\PHOTO\IMAGES\IMG-20210212-WA0008.jpg"

    # Configurar banco de dados
    setup_database()

    # Processar imagem
    detected_objects = process_image(image_path)

    # Salvar no banco de dados
    save_to_database(detected_objects)

    print(f"{len(detected_objects)} objetos detectados e salvos no banco de dados")