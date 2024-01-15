import re
import json
import string
import array
from colorama import Fore
import mod_listidentify
import mod_literal
import mod_concept
import mod_class
import database

def register(IDC,prop,IDP):
    IDprop = mod_class.getClass(prop)
    IDliteral = 0

    qr = "select * from brapci_rdf.rdf_data "
    qr += f"where d_r1 = {IDC}"
    qr += f" AND d_p = {IDprop}"
    qr += f" AND d_r2 = {IDP}"
    row = database.query(qr)
    if row == []:
        qri = "insert into brapci_rdf.rdf_data "
        qri += "(d_r1, d_r2, d_p, d_literal)"
        qri += " values "
        qri += f"({IDC},{IDP},{IDprop},{IDliteral});"
        database.insert(qri)
        row = database.query(qr)

    return row


def register_literal(IDC,prop,name,lang):
    IDprop = mod_class.getClass(prop)
    IDliteral = mod_literal.register(name,lang)

    qr = "select * from brapci_rdf.rdf_data "
    qr += f"where d_r1 = {IDC}"
    qr += f" AND d_p = {IDprop}"
    qr += f" AND d_literal = {IDliteral}"
    row = database.query(qr)
    if row == []:
        qri = "insert into brapci_rdf.rdf_data "
        qri += "(d_r1, d_r2, d_p, d_literal)"
        qri += " values "
        qri += f"({IDC},0,{IDprop},{IDliteral});"
        database.insert(qri)
        row = database.query(qr)

    return row
