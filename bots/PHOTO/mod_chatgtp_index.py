import cv2
import database
import uuid
from ultralytics import YOLO



# Função para detectar objetos e pessoas em uma imagem
def detect_objects(image_path):
    model = YOLO("yolov8n.pt")  # Modelo YOLOv8 pré-treinado
    image = cv2.imread(image_path)
    results = model(image)

    detected_objects = []
    person_count = 1

    for result in results:
        for box in result.boxes:
            class_id = int(box.cls.item())
            label = model.names[class_id]

            # Atribuir nome especial para pessoas
            if label == "person":
                obj_id = f"Pessoa_{person_count}"
                person_count += 1
            else:
                obj_id = str(uuid.uuid4())[:8]  # ID aleatório para outros objetos

            detected_objects.append({"label": label, "id": obj_id})

    return detected_objects

if __name__ == "__main__":
    database.create_database()
    database.create_table()

    #image_path = "IMAGE/IMG-20210219-WA0027.jpg"  # Substituir pelo caminho real da imagem
    image_path = r"E:\Projeto\www\Brapci3.1\bots\PHOTO\IMAGES\IMG-20210219-WA0027.jpg"
    image_path = r"E:\Projeto\www\Brapci3.1\bots\PHOTO\IMAGES\20211002_122120.jpg"
    image_path = r"E:\Projeto\www\Brapci3.1\bots\PHOTO\IMAGES\IMG-20210212-WA0008.jpg"

    image_name = image_path.split("/")[-1]
    detected_objects = detect_objects(image_path)

    database.save_to_database(image_name, detected_objects)
    print("Detecção concluída e salva no banco de dados!")