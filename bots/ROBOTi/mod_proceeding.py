import database
import oaipmh_request
import mod_setSpec
import oaipmh_ListIdentifiers

def harvesting():
    print("HELLO")
    qr = "select is_url_oai, is_oai_token, is_source, id_is "
    qr += "from brapci.source_issue "
    qr += "where is_status = 0 limit 1"
    row = database.query(qr)

    if not row == []:
        print(row)

        # GET SETSPEC
        URL = str(row[0][0])
        JNL= str(row[0][2])
        print("URL=>",URL)
        XML = getSetSpec(URL,JNL)
        print('Status:',XML['status'])

def getSetSpec(url,JNL):
    LINK = url + '?verb=ListSets'
    xml = oaipmh_request.get(LINK)

    if (xml['status'] == '200'):
        oaipmh_ListIdentifiers.xml_setSpecList(xml,JNL)


    return xml