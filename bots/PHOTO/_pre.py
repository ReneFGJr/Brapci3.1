import cv2
import os

# Verificar se os arquivos do modelo existem
required_files = {
    "yolov3.weights": "https://pjreddie.com/media/files/yolov3.weights",
    "yolov3.cfg": "https://github.com/pjreddie/darknet/blob/master/cfg/yolov3.cfg",
    "coco.names": "https://github.com/pjreddie/darknet/blob/master/data/coco.names"
}

for file in required_files:
    if not os.path.exists(file):
        print(f"ERRO: Arquivo {file} não encontrado!")
        print(f"Faça download em: {required_files[file]}")
        exit()

# Verificar versão do OpenCV
print("Versão OpenCV:", cv2.__version__)  # Deve ser >= 4.5.5

# Tentar carregar o modelo com verificação adicional
try:
    net = cv2.dnn.readNet("yolov3.weights", "yolov3.cfg")
    print("Modelo YOLO carregado com sucesso!")
except Exception as e:
    print("Erro ao carregar modelo:", str(e))
    print("Soluções possíveis:")
    print("1. Verifique se os arquivos estão corrompidos (tamanho esperado para yolov3.weights: ~237MB)")
    print("2. Use OpenCV 4.5.5: pip install opencv-python==4.5.5.64")
    print("3. Experimente YOLOv3-tiny (mais leve):")
    print("   - weights: https://pjreddie.com/media/files/yolov3-tiny.weights")
    print("   - cfg: https://github.com/pjreddie/darknet/blob/master/cfg/yolov3-tiny.cfg")