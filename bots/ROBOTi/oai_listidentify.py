# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-24
# @Title: oai_listidentify
import database
table = 'brapci_oaipmh.oai_listidentify'

def register(ID,JNL):
    print("HELLO")

def chageStatus(ID,status):
    qu = "update " + table + " set status = " + str(status) + ", oai_rdf = 0 where oai_rdf = " + str(ID)
    database.update(qu)