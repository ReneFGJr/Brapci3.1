import database

def register(JNL,ISSUE,WORK):
    qr = "select * from brapci.source_issue_work "
    qr += f"where siw_journal = {JNL} "
    qr += f"and siw_issue = {ISSUE} "
    qr += f"and siw_work_rdf = {WORK} "
    row = database.query(qr)

    if row == []:
        qi = "insert into brapci.source_issue_work "
        qi += "(siw_journal, siw_issue, siw_work_rdf)"
        qi += " values "
        qi += f"({JNL},{ISSUE},{WORK})"
        database.insert(qi)