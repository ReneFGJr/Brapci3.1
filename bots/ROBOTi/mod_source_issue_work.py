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

def checkIssueWork():
    qr = """
        SELECT d1.d_r1, d1.d_r2, d2.d_r1, jnl_name, d2.d_p, id_jnl, d1.id_d
            FROM brapci_rdf.rdf_data as d1
            INNER JOIN brapci_rdf.rdf_concept as c1 ON d_r1 = c1.id_cc
            INNER JOIN brapci_rdf.rdf_class as cl1 ON c1.cc_class = cl1.id_c
            INNER join brapci_rdf.rdf_concept as c2 ON d_r2 = c2.id_cc and c2.cc_status <> 9
            INNER join brapci_rdf.rdf_data as d2 ON d1.d_r2 = d2.d_r2
            INNER join brapci.source_source ON d2.d_r1 = source_source.jnl_frbr
            where d1.d_p = 31 and cl1.c_class = 'Issue'

            and id_jnl = 2
    """
    row = database.query(qr)
    for r in row:
        register(r[5],r[0],r[1])
