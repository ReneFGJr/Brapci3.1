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