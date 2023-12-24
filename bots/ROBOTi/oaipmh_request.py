# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-24
# @Title: Modulo de request

import requests
import urllib3
from colorama import Fore

urllib3.disable_warnings()

headers = {'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) '\
        'AppleWebKit/537.36 (KHTML, like Gecko) '\
        'Chrome/75.0.3770.80 Safari/537.36'}
zheaders = {
    "Accept": "*/*",
    "Accept-Encoding": "gzip, deflate",
    "Host": "brapci.inf.br",
    "User-Agent": "python-requests/2.26.0",
  }

xheaders = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:98.0) Gecko/20100101 Firefox/98.0",
        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
        "Accept-Language": "en-US,en;q=0.5",
        "Accept-Encoding": "gzip, deflate",
        "Connection": "keep-alive",
        "Upgrade-Insecure-Requests": "1",
        "Sec-Fetch-Dest": "document",
        "Sec-Fetch-Mode": "navigate",
        "Sec-Fetch-Site": "none",
        "Sec-Fetch-User": "?1",
        "Cache-Control": "max-age=0",
    }

def get(LINK):
    status_code = '000'

    data = {'v': 1}
    timeout = 30

    try:
        web = requests.Session()
        #cnt = web.get(LINK,verify=False, timeout=timeout, headers=headers, allow_redirects=True)
        cnt = web.get(LINK, timeout=timeout, headers=headers)
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