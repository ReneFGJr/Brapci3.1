import datetime
import database
from colorama import Fore

def nextGetRecords(status):
    limit = 10
    if (status == 5):
        limit = 250
    if (status == 6):
        limit = 250
    if (status == 7):
        limit = 25
    cp = "id_oai, oai_identifier, jnl_url_oai, jnl_name_abrev, oai_setSpec, oai_rdf, "
    cp += "id_jnl, s_id, oai_issue, is_url_oai, is_source_issue, jnl_collection"
    cp += "is_url_oai "
    qr = f"select {cp} from brapci_oaipmh.oai_listidentify "
    qr += " inner join brapci.source_source on oai_id_jnl = id_jnl "
    qr += " left join brapci.source_issue on oai_issue = id_is "
    qr += " inner join brapci_oaipmh.oai_setspec on oai_setSpec = id_s "
    qr += f" where oai_status = {status} "
    qr += " order by oai_update "
    qr += f" limit {limit} "
    row = database.query(qr)
    return row


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

def task_active(task):
    qr = f"select * from brapci_bots.tasks where task_id = '{task}'"
    row = database.query(qr)
    if row == []:
        return False
    else:
        return True

def task_start(task,prio=0):
    if not (task_active(task)):
        qi = f"insert into brapci_bots.tasks (task_id, task_propriry, task_offset) values ('{task}',{prio},0)"
        row = database.insert(qi)

def task_update(task,offset=0):
    if not (task_active(task)):
        qi = f"update brapci_bots.tasks set task_offset = {offset} where task_id = '{task}'"
        row = database.update(qi)

def task_remove(task,prio=0):
    if task_active(task):
        qr = f"delete from brapci_bots.tasks where task_id = '{task}'"
        row = database.update(qr)
