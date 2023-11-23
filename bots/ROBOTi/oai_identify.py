import mysql.connector
from mysql.connector import errorcode
import urllib.request
import xml.etree.ElementTree as ET

import sys
#sys.path.insert(0, '../')

from oaipmh.client import Client
from oaipmh.metadata import MetadataRegistry, oai_dc_reader

import env
# identify info

registry = MetadataRegistry()
registry.registerReader('oai_dc', oai_dc_reader)
URL = 'https://seer.ufrgs.br/emquestao/oai'
#https://seer.ufrgs.br/emquestao/oai?verb=Identify
client = Client(URL, registry)

identify = client.identify()
print("Repository name: {0}".format(identify.repositoryName()))
print("Base URL: {0}".format(identify.baseURL()))
print("Protocol version: {0}".format(identify.protocolVersion()))
print("Granularity: {0}".format(identify.granularity()))
print("Compression: {0}".format(identify.compression()))
print("Deleted record: {0}".format(identify.deletedRecord()))
print("Admin Email: {0}".format(identify.adminEmails()))
print("Descriptions: {0}".format(identify.descriptions()))
