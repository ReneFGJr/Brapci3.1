import database

def log(task, status):
    qi = 'insert brapci_bots.cron_logs '
    qi += '(log_type, log_status)'
    qi += ' values '
    qi += f"('{task}','{status}')"
    database.insert(qi)
    print("Log registrado ",task,status)
