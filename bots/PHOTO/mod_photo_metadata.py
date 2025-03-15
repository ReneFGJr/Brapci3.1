from PIL import Image
from PIL.ExifTags import TAGS, GPSTAGS

# Função para extrair metadados EXIF, incluindo geolocalização
def get_metadata(image_path):
    image = Image.open(image_path)
    exif_data = image._getexif()

    if not exif_data:
        return None

    exif = {}
    gps_info = {}

    for tag, value in exif_data.items():
        tag_name = TAGS.get(tag, tag)
        if tag_name == "GPSInfo":
            for key in value.keys():
                gps_info[GPSTAGS.get(key, key)] = value[key]
        else:
            exif[tag_name] = value

    exif["GPSInfo"] = gps_info
    return exif

# Extraindo metadados
if __name__ == "__main__":
    image_path = "sample.jpg"
    metadata = get_metadata(image_path)
