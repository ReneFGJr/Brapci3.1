#!/usr/bin/env python3
# @Author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
# @Data: 2023-12-22
# @Title: Robos da Brapci (ROBOTi)

import os
import sys
import roboti_help
import roboti_task
import roboti_clear
import mod_setSpec
import mod_abstract
import mod_listidentify
import mod_source
import mod_data
import mod_article
import mod_subject
import oaipmh_ListIdentifiers
import oaipmh_getRecord
import mod_editais
import mod_elasticsearch
import mod_issue
import mod_dataset
import mod_ontology
import mod_lattes
import mod_author
import mod_literal
import mod_logs
import mod_section
import database
import mod_concept
import mod_cited
import mod_pdf
import mod_especial_rules
import mod_proceeding
import mod_backup_mysql
import mod_thesa
import mod_gender
import mod_person
import mod_statistics
import socket
from colorama import Fore

def version():
    return "v0.24.04.01"

def auto():
    print("Robo Automático CRON")

    qr = "select * from brapci_bots.tasks"
    row = database.query(qr)
    tks = 'CRON'

    for tk in row:
        tks = tk[1]
        if (tks == 'HARVESTING'):
            run(['ROBOTI','1'])
        if (tks == 'GETRECORD'):
            run(['ROBOTI','2'])
        if (tks == 'PROC_RECORD'):
            run(['ROBOTI','3'])
        if (tks == 'PROC_ISSUE'):
            run(['ROBOTI','4'])
        if (tks == 'PROC_WORK'):
            ProcessArticle()
        if (tks == 'BACKUP'):
            mod_backup_mysql.backup_all()

        ########### mod_proceeding
        if (tks == 'PROCEEDING'):
            mod_proceeding.harvesting()

    mod_logs.log(tks,0)
    return ""

def run(parm):
    act = parm[1]
    print(Fore.BLUE+"Function: ",act)
    print(Fore.WHITE)

    if (act == '0'):
        mod_concept.UpdateUse()

    #************************************************* Functions
    if ((act == 'help') or (act == '?')):
        roboti_help.help()
    if (act =='backup'):
        mod_backup_mysql.backup_all()
    #********************** ListIdentiers - LOOP
    if (act == '1'):
        ListIdentiers()
    #********************** ListIdentiers - LOOP
    if (act == '2'):
        GetRecord()

    #********************** ListIdentiers - LOOP
    if (act == 'startHarvesting'):
        startHarvesting()

    #********************** Preprocess - LOOP
    if (act == '3'):
        ProcessRecord()

    #********************** CheckEdition - LOOP - 6
    if (act == '4'):
        ProcessRecordIssue()

    #********************** Article - LOOP - 7
    if (act == '5'):
        ProcessArticle()

    #******************** Proceeding */
    if (act == '10'):
        mod_section.classificationSection()
    if (act == '11'):
        mod_proceeding.harvesting()
    if (act == '12'):
        mod_proceeding.issue()

    if (act == '100'):
        lp = mod_data.DataDouble()
    if (act == '101'):
        lp = mod_ontology.checkDataInverse()
    if (act == '102'):
        lp = mod_ontology.checkLiteralExist()
    if (act == '103'):
        lp = mod_ontology.classification()
    if (act == '105'):
        lp = mod_ontology.checkDataConceptExist()
    if (act == '110'):
        lp = mod_ontology.checkData()
    if (act == '111'):
        lp = mod_ontology.checkDataNull()
    if (act == '112'):
        mod_data.invert()
    if (act == '113'):
        #hasTitle
        mod_data.literal_double(30)
    if (act == '114'):
        #hasAbstract
        mod_data.literal_double(86)
    if (act == '120'):
        lp = mod_ontology.resume()

    if (act == 'statistics'):
        mod_statistics.get_statistics()

    if (act == 'pdf'):
        mod_pdf.harvestingPDF()
    if (act == 'pdfv'):
        mod_pdf.validaPDF()
        mod_especial_rules.rule01()

    if (act == 'doi'):
        mod_cited.locate()
        mod_cited.cited()
        mod_cited.longCited()

    if (act == 'cited'):
        #mod_cited.extrairAutores()
        mod_cited.remove_spaces()
        mod_cited.refatureABNT()
        mod_cited.categorizeBook()
        mod_cited.categorizeYear()
        #mod_cited.categorizeCited()

    if (act == 'ontology'):
        mod_concept.UpdateUse()
        mod_ontology.classification()
        mod_ontology.checkData()
        mod_ontology.checkLiteralExist()
        mod_ontology.checkDataNull()
        mod_ontology.checkDataInverse()
    if (act == 'editais'):
        mod_editais.editais()

    if (act == 'check'):
        mod_concept.UpdateUse()
        mod_concept.remissives()

        mod_ontology.classification()
        mod_literal.check_utf8()
        mod_literal.check_duplicate()
        mod_literal.check_double_name()
        mod_literal.check_trim()
        mod_literal.check_all()
        mod_literal.check_title()
        mod_literal.check_end_dot()
        mod_elasticsearch.remove_editorial()
        mod_dataset.check_type()
        mod_ontology.checkData()
        mod_concept.removeElastic()

        mod_author.check_dupla_remissiva()
        mod_author.check_remissiva()
        mod_author.check_duplicate()

        mod_dataset.check_duplicate()
        mod_dataset.check_pbci()
        mod_subject.check_subject_sql()
        mod_subject.check_duplicate()
        mod_subject.removeOrfa()

        mod_literal.check_double_literal('hasTitle')
        mod_literal.check_double_literal('hasAbstract')

        mod_pdf.harvestingPDF()


    if (act == 'keyword'):
        print("=================KEYWORDS")
        mod_subject.removeOrfa()
        mod_subject.check_duplicate()

    if (act == 'person'):
        mod_abstract.check_double()
        sys.exit()
        mod_person.check()
        mod_gender.check()


    #################### LITERAL
    if (act == '140'):
        lp = mod_literal.check_duplicate()
    if (act == '150'):
        lp = mod_literal.check_trim()
    if (act == '151'):
        lp = mod_literal.check_all()
    if (act == '152'):
        lp = mod_literal.check_double_name()
    if (act == '153'):
        lp = mod_literal.check_title()
    if (act == '154'):
        mod_literal.check_utf8()
    if (act == '156'):
        mod_literal.check_end_dot()
    if (act == '157'):
        mod_data.revalid()
    if (act == '158'):
        lp = mod_literal.check_double_literal('hasTitle')
    if (act == '159'):
        lp = mod_literal.check_double_literal('hasAbstract')

    ################### Cited
    if (act == '160'):
        mod_cited.cited()
    if (act == '161'):
        mod_cited.categorizeCitedByElastic()

    ################### Thesa
    if (act == '170'):
        mod_thesa.check_subject_thesa()

    ################### ElasticSearch
    if (act == '180'):
        mod_elasticsearch.dataset_news()
    if (act == '181'):
        print("REINDEXAR")
        mod_elasticsearch.reindex()
    if (act == '182'):
        mod_thesa.IA_thesa()
    if (act == 'thesa'):
        mod_thesa.IA_thesa()
        mod_thesa.check_subject_thesa()
    if (act == '183'):
        mod_elasticsearch.remove_editorial()
    if (act == '184'):
        mod_concept.removeElastic()


    ################### Author
    if (act == '200'):
        lp = mod_author.check_duplicate()
    if (act == '201'):
        lp = mod_author.check_dupla_remissiva()
    if (act == '202'):
        lp = mod_author.check_remissiva()
    if (act == '205'):
        lp = mod_author.check_remissivaDB()
    if (act == '206'):
        lp = mod_author.removeCollon()

    ################### Subject
    if (act == '210'):
        lp = mod_subject.check_subject_sql()
        lp = mod_subject.check_duplicate()
    if (act == '211'):
        lp = mod_subject.check_remissiva()

    ################### Works
    if (act == '220'):
        lp = mod_dataset.check_duplicate()
    if (act == '221'):
        mod_dataset.check_double_issue()

    ################### Works
    if (act == '230'):
        lp = mod_dataset.check_type()
    if (act == '231'):
        lp = mod_dataset.check_pbci()
        mod_especial_rules.rule01()

    if (act == '240'): # Rules
        mod_especial_rules.rule01()


    if (act == 'lattesK'):
        file = parm[2]
        hd = parm[3]
        lp = mod_lattes.import_file(file,hd)

    #********************** Clear
    elif (act == 'clear'):
        roboti_clear.clear(0)
    elif (act == 'testdb'):
        import bot_test_db
        bot_test_db.dbtest()

    print(Fore.WHITE)

def DataDouble():
    mod_data.removeDouble()

def ProcessArticle(): ############################# 5
    # Phase I - get Next Records
    #reg = roboti_task.nextGetRecords(6)
    reg = roboti_task.nextGetRecords(7)
    if (reg == []):
        print("Removendo TASK PROC_WORK")
        roboti_task.task_remove('PROC_WORK')
        #//print("Incluindo TASK PROC_WORK")
        #//roboti_task.task_start('PROC_WORK')

    # Phase II - Processa arquivos
    if (reg != []):
        for it in reg:
            mod_article.process(it)

def ProcessRecordIssue():
    # Phase I - get Next Records
    reg = roboti_task.nextGetRecords(6)
    if (reg == []):
        print("Removendo TASK PROC_ISSUE")
        roboti_task.task_remove('PROC_ISSUE')
        print("Incluindo TASK PROC_WORK")
        roboti_task.task_start('PROC_WORK')

    # Phase II - Processa arquivos
    if (reg != []):
        for it in reg:
            mod_issue.process(it)

def ProcessRecord():
    # Phase I - get Next Records
    reg = roboti_task.nextGetRecords(5)

    if (reg == []):
        print("Removendo TASK PROC_RECORD")
        roboti_task.task_remove('PROC_RECORD')
        print("Incluindo TASK PROC_ISSUE")
        roboti_task.task_start('PROC_ISSUE')

    # Phase II - Processa arquivos
    if (reg != []):
        for it in reg:
            oaipmh_getRecord.process(it)

def GetRecord():
    # Phase I - get Next Records
    reg = roboti_task.nextGetRecords(1)

    if (reg == []):
        print("Removendo TASK GETRECORD")
        roboti_task.task_remove('GETRECORD')
        print("Incluindo TASK PROC_RECORD")
        roboti_task.task_start('PROC_RECORD')

    # Phase II - Coleta arquivos
    if (reg != []):
        for it in reg:
            oaipmh_getRecord.get(it)

    # Phase III - Fim do processo
    print(Fore.GREEN+"... Fim do processamento"+Fore.WHITE)

def startHarvesting():
    if not roboti_task.task_active('HARVESTING'):
        roboti_task.task_start('HARVESTING')
        print("[Ativar Harvesting]")
        return ""
    else:
        print("Coletardor já está ativo")


def ListIdentiers():
    if not roboti_task.task_active('HARVESTING'):
        return ""

    # Phase I
    reg = roboti_task.nextHarvesting()

    if (reg == []):
        print("Removendo TASK HARVESTING")
        roboti_task.task_remove('HARVESTING')
        print("Incluindo TASK GETRECORD")
        roboti_task.task_start('GETRECORD')

    # Phase II - Valie
    if not (roboti_task.valid(reg)):
        return False

    import oai_journal
    URL = reg[0][1]
    JNL = reg[0][0]
    token = reg[0][4]

    oai_journal.getSetSpec(URL,JNL)

    # Phase III - GetList
    jnl = reg[0][0]
    xml = oaipmh_ListIdentifiers.get(reg[0])

    # Phase IV - Check and Process XML File
    if (xml['status'] == '200'):
        # Phase IVa - Get setSpecs
        print("XXXXXXXXXXXXX IVa XX")
        setSpec = oaipmh_ListIdentifiers.xml_setSpec(xml)
        # Phase IVb - Registers setSpecs
        print("XXXXXXXXXXXXX IVb XX")
        setSpec = mod_setSpec.process(setSpec,reg)
        # Phase IVc - Identifica Identify
        print("XXXXXXXXXXXXX IVc XX")
        identifies = oaipmh_ListIdentifiers.xml_identifies(xml,setSpec,jnl)
        # Pahse IVd - Registra Identify
        print("XXXXXXXXXXXXX IVd XX")
        print("JNL",jnl)
        mod_listidentify.registers(identifies,jnl)
        print("XXXXXXXXXXXXX IVe XX")

        print("Status",xml['status'])
    #Phase V - Token
    if (xml['status'] == '200'):
        token = mod_source.token(xml)
        mod_source.update(jnl,'100',token)
        if token == '':
            print(Fore.GREEN+"Fim da coleta"+Fore.WHITE)
            loop = 0
        else:
            print(Fore.YELLOW+"... Reprocessamento da Coleta "+Fore.GREEN+token+Fore.WHITE)
            loop = 1
        return loop
    else:
        mod_source.update(jnl,xml['status'],'')



########################################### Início
print("ROBOTi",version())
print("===============================================")
hostname = socket.gethostname()
print(hostname)

if (hostname == 'DESKTOP-M0Q0TD7'):
    diretorio = os.getcwd()
else:
    diretorio = '/data/Brapci3.1/bots/ROBOTi'

print("Diretório",diretorio)
os.chdir(diretorio)

if (len(sys.argv) > 1):
    parm = sys.argv
    run(parm)
else:
    auto()