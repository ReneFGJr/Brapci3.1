import os, sys
import requests
import zipfile
import mimetypes
from datetime import datetime

def extract_lattes(lattes_id):
    log = []
    data = {'id': lattes_id}

    if len(lattes_id) != 16:
        return ""

    url = f"https://brapci.inf.br/ws/api/?verb=lattes&q={lattes_id.strip()}"
    log.append(f"<hr>{url}<br>")
    log.append(f"Coletando Lattes {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}<br>")

    if not file_is_updated(lattes_id):
        zip_dir = "../.tmp/Zip"
        lattes_dir = "../.tmp/Lattes"
        os.makedirs(zip_dir, exist_ok=True)
        os.makedirs(lattes_dir, exist_ok=True)

        zip_path = os.path.join(zip_dir, "lattes.zip")
        filename = get_lattes_filename(lattes_id)

        try:
            response = requests.get(url)
            if not response.content:
                log.append("ERRO: o arquivo está vazio<br/>" + url)
                update_harvest_error(lattes_id)
                return "".join(log)

            with open(zip_path, 'wb') as f:
                f.write(response.content)

            mime_type = mimetypes.guess_type(zip_path)[0]
            if mime_type == 'application/json':
                log.append("ERRO<br>")
                json_data = response.json()
                log.append(f"{json_data.get('erro', '')}<br>{json_data.get('description', '')}<br>")
                return "".join(log)

            with zipfile.ZipFile(zip_path, 'r') as zip_ref:
                zip_ref.extractall(lattes_dir)

            if not os.path.exists(filename):
                log.append(f"Erro ao abrir o arquivo {filename}")
                return "".join(log)

        except Exception as e:
            log.append(f"Erro durante a coleta: {str(e)}<br>")
            return "".join(log)

    else:
        log.append("CACHED<br>")

    return "".join(log)

# Funções auxiliares fictícias - devem ser implementadas conforme sua estrutura real:
def file_is_updated(lattes_id):
    # Lógica para verificar se o arquivo foi atualizado recentemente
    return False

def get_lattes_filename(lattes_id):
    # Gera o caminho para o arquivo XML extraído do Lattes
    return f"../.tmp/Lattes/{lattes_id}.xml"

def update_harvest_error(lattes_id):
    # Atualiza status de erro na base de dados
    print(f"[ERRO] Atualização falhou para ID {lattes_id}")


if __name__ == "__main__":
    # Verificar se o número correto de argumentos foi fornecido
    if len(sys.argv) != 2:
        print("Uso: python3 mod_lattes.py IDlattes")
        sys.exit(1)
    else:
        print(extract_lattes(sys.argv[1]))