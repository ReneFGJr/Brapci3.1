import datetime
import database
from colorama import Fore

def nextHarvesting():
    global sourceName
    global URL
    now_time = datetime.datetime.now()
    day = now_time.day
    month = now_time.month

    cp = "id_jnl, jnl_url_oai, jnl_oai_last_harvesting, jnl_name, jnl_oai_token"
    q = f"select {cp} "
    q += " from brapci.source_source \n"
    q += " where "
    q += " (jnl_historic = 0)"
    q += " and (jnl_active = 1)"
    q += " and (jnl_url_oai <> '')"
    q += f" and (jnl_collection <> 'EV')"
    q += " and ((year(update_at) < 2000)"
    q += f"      or (MONTH(jnl_oai_last_harvesting) <> {month})"
    q += " )"
    q += " order by jnl_oai_last_harvesting"
    q += " limit 1"
    row = database.query(q)
    return row

def valid(row):
    if row != []:
        print(Fore.YELLOW+"... Harvesting: "+Fore.GREEN+row[0][3]+Fore.WHITE)
        return True
    else:
        return False
