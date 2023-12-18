
import xmltodict

def convertXMLtoJSON(file):
    print(file)
    f = open(file, "r")
    docXML = f.read()
    f.close()
    doc = xmltodict.parse(docXML)
    print(doc)