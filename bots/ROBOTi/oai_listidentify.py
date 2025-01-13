# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-24
# @Title: oai_listidentify
import database
table = 'brapci_oaipmh.oai_listidentify'

def register(ID,JNL):
    print("HELLO")

def chageStatusID(ID,status):
    qu = "update " + table + " set status = " + str(status) + " where id_oai = " + str(ID)
    database.update(qu)

def zeraRDF(ID):
    qu = "update " + table + " set oai_rdf = 0 where id_oai = " + str(ID)
    database.update(qu)

def chageStatus(ID,status):
    qu = "update " + table + " set status = " + str(status) + ", oai_rdf = 0 where oai_rdf = " + str(ID)
    database.update(qu)