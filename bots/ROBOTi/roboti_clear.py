import database
import datetime
from colorama import Fore

def clear(ID):

    now = datetime.datetime(1980,1,1)
    str_now = now.date().isoformat()
    if ID > 0:
        qr = "update brapci.source_source \n "
        qr += " set jnl_oai_status = '500', \n "
        qr += " update_at = null, \n "
        qr += f" jnl_oai_last_harvesting = '{str_now}' \n "
        qr += f" where id_jnl = {ID}"
        database.query(qr)
        print(Fore.GREEN+"Recoleta habilidata com sucesso! "+Fore.WHITE)
    else:
        qr = "update brapci.source_source \n "
        qr += " set jnl_oai_status = '500', \n "
        qr += f" update_at = '{str_now}', \n "
        qr += " jnl_oai_last_harvesting = '1900-01-01' \n "
        qr += " where jnl_active = 1 and jnl_historic = 0"
        print(qr)
        database.query(qr)
        print(Fore.GREEN+"Recoleta habilidata com sucesso! "+Fore.WHITE)
