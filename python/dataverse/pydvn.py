#pip install -U pyDataverse
#https://curlconverter.com/python/
from pyDataverse.api import NativeApi
import os, sys, requests, json


def connectDVN():
    filen = "../.env"
    if not os.path.isfile(filen):
        print('Configuration file .env not fount, copy env to .env and configure it!')
        sys.exit()


    with open(filen) as dcjson:
        data = json.load(dcjson)

    i = data['dataverse']
    BASE_URL = i['baseurl']
    API_TOKEN = i['apikey']

    return {"BASE_URL":BASE_URL,"API_TOKEN":API_TOKEN}

def createDataset(ROOT,NAME):
    return True


def addFIle(DOI,file):
    env = connectDVN()

    headers = { 'apikey': env['API_TOKEN'],'key': env['API_TOKEN']}
    params = { }
    try:
        files = { 'file': open(file, 'rb'), 'jsonData': (None, '{"description":"My description.","directoryLabel":"source/code","categories":["Code"], "restrict":"true", "tabIngest":"false"}'), }
    except Error as err:
        print(f"Error: '{err}'")

    ################# request
    try:
        URL = env['BASE_URL'] + 'api/datasets/:persistentId/add?persistentId='+DOI+"&key="+env['API_TOKEN']

        response = requests.post(URL,
            params=params,
            headers=headers,
            files=files,
        )
        print(response)

    except Error as err:
        print(f"Error: '{err}'")
