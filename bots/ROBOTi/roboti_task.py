import datetime
import database
from colorama import Fore

def nextHarvesting():
    global sourceName
    global URL
    now_time = datetime.datetime.now()
    day = now_time.day
    month = now_time.month

    q = f"select id_jnl, jnl_url_oai, jnl_oai_last_harvesting, jnl_name \n"
    q += " from brapci.source_source \n"
    q += f" where ((DAY(jnl_oai_last_harvesting) <> {day}) \n"
    q += f" or (MONTH(jnl_oai_last_harvesting) <> {month}) \n"
    q += f" or (jnl_oai_last_harvesting is null) \n"
    q += f" or ((update_at = '1900-01-01') or (update_at is null)))\n"
    q += f" and ((jnl_historic = 0) \n"
    q += f" and (jnl_url_oai <> '')"
    q += f" and (jnl_collection <> 'EV')) \n"
    q += " order by jnl_oai_last_harvesting \n"
    q += " limit 1 "

    row = database.query(q)

    print("NEXT",q)
    print(Fore.GREEN,row,Fore.WHITE)