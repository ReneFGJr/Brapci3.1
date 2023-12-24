# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-24
# @Title: Modulo de request

import requests
import urllib3
from colorama import Fore

urllib3.disable_warnings()

xheaders = {'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) '\
        'AppleWebKit/537.36 (KHTML, like Gecko) '\
        'Chrome/75.0.3770.80 Safari/537.36'}

headers = {
    'authority': 'www.google.com',
    'accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
    'accept-language': 'en-US,en;q=0.9',
    'cache-control': 'max-age=0',
    'cookie': 'SID=ZAjX93QUU1NMI2Ztt_dmL9YRSRW84IvHQwRrSe1lYhIZncwY4QYs0J60X1WvNumDBjmqCA.; __Secure-#..',
    'sec-ch-ua': '"Not/A)Brand";v="99", "Google Chrome";v="115", "Chromium";v="115"',
    'sec-ch-ua-arch': '"x86"',
    'sec-ch-ua-bitness': '"64"',
    'sec-ch-ua-full-version': '"115.0.5790.110"',
    'sec-ch-ua-full-version-list': '"Not/A)Brand";v="99.0.0.0", "Google Chrome";v="115.0.5790.110", "Chromium";v="115.0.5790.110"',
    'sec-ch-ua-mobile': '?0',
    'sec-ch-ua-model': '""',
    'sec-ch-ua-platform': 'Windows',
    'sec-ch-ua-platform-version': '15.0.0',
    'sec-ch-ua-wow64': '?0',
    'sec-fetch-dest': 'document',
    'sec-fetch-mode': 'navigate',
    'sec-fetch-site': 'same-origin',
    'sec-fetch-user': '?1',
    'upgrade-insecure-requests': '1',
    'user-agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
    'x-client-data': '#..',
}

def get(LINK):
    status_code = '000'

    data = {'v': 1}
    timeout = 30

    try:
        cnt = requests.get(LINK,verify=False, data=data, timeout=timeout, headers=headers, allow_redirects=True)
    except requests.exceptions.SSLError:
        pass
    except Exception as e:
        print(Fore.RED+"ERRO Request:",cnt.status_code,e,Fore.WHITE)
        print(f"... Erro request - OAIPMH - LisyIdentifiers")
        status_code = '500'
        return {'content':'','status':status_code}
    finally:
        try:
            status_code = '200'
            return {'content':cnt.text,'status':status_code}
        except Exception as e:
            status_code = '501'
            print("... ERRO: 404",e)
            print(f"... cnt.text empty - OAIPMH - LisyIdentifiers")
            return {'content':'','status':status_code}