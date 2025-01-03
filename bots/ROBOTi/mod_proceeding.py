import database
import oaipmh_request
import mod_setSpec
import oaipmh_ListIdentifiers
import mod_listidentify
import mod_source
import oai_issue
import mod_issue
from colorama import Fore
import sys

def harvesting():
    qr = "select is_url_oai, is_oai_token, is_source, id_is, is_oai_token, "
    qr += " is_source, is_source_issue, is_year, is_vol_roman, is_nr, "
    qr += " jnl_frbr "
    qr += "from brapci.source_issue "
    qr += " inner join brapci.source_source ON is_source = id_jnl "
    qr += "where is_status = 0 limit 1"
    row = database.query(qr)

    if not row == []:

        # GET SETSPEC
        URL = str(row[0][0])
        JNL= str(row[0][2])
        idJNL= str(row[0][5])
        ISSUE = str(row[0][3])
        token = str(row[0][4])
        rdf_ISSUE = str(row[0][6])
        year = str(row[0][7])
        vol = str(row[0][8])
        nr = str(row[0][9])
        rdf_JOURNAL = str(row[0][10])

        print('URL',URL)
        print('Token:',token)

        RSP = oai_issue.getSetSpec(URL,idJNL)

        print('Status:',RSP)

        if RSP == '200':
            print(f"ID-ISSUE: {rdf_ISSUE}\nJNL {rdf_JOURNAL}\nYEAR:{year}\nVOL: {vol},{nr}")
            if rdf_ISSUE == '0':
                print("Criando ISSUE-RDF")
                rdf_ISSUE = mod_issue.create_issue_rdf(idJNL,year,vol,nr)
                qu = "update brapci.source_issue set is_source_issue = {rdf_ISSUE} where id_is = {ISSUE}"
                database.update(qu)

            xml = oai_issue.getListIdentifiers(URL,idJNL, token, rdf_ISSUE)
            mod_issue.updateSatus(ISSUE,1)
        else:
            print("FIM")

def updateIssue(issue,token,status=0):
    qu = "update brapci.source_issue set "
    qu += f"is_oai_token = '{token}', is_status = {status}"
    qu += f" where id_is = {issue}"
    database.update(qu)

def getSetSpec(url,JNL):
    LINK = url + '?verb=ListSets'

    print("...Coletando",LINK)
    xml = oaipmh_request.get(LINK)

    print(xml)

    if (xml['status'] == '200'):
        oaipmh_ListIdentifiers.xml_setSpecList(xml,JNL)


    return xml