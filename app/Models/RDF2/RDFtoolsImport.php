<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFtoolsImport extends Model
{
    protected $DBGroup          = 'rdf2';
    protected $table            = 'rdftoolsimports';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    function zeraDB()
    {
        $sql = "TRUNCATE `rdf_class_domain`";
        $this->db->query($sql);

        $sql = "TRUNCATE `rdf_class_range`";
        $this->db->query($sql);
    }

    function import($file)
    {
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $RDFclassDomain = new \App\Models\RDF2\RDFclassDomain();
        $RDFclassRange = new \App\Models\RDF2\RDFclassRange();
        $RDFproperty = new \App\Models\RDF2\RDFproperty();

        if (file_exists($file)) {
            $this->zeraDB();

            $xml = simplexml_load_file($file);
            /********************** Prefix - NameSpace */
            $namespaces = $xml->getNamespaces(true);
            $this->getNameSpace($namespaces);

            /********************* Content - READ */
            $txt = file_get_contents($file);
            $txt = troca($txt, 'owl:', 'owl_');
            $txt = troca($txt, 'rdf:', 'rdf_');
            $txt = troca($txt, 'rdfs:', 'rdfs_');
            $txt = troca($txt, 'xml:', 'xml_');
            $txt = troca($txt, 'xmlns:', 'xmlns_');
            $xml = simplexml_load_string($txt);

            $url = [];

            /************ Classes **************************************/
            foreach ($xml as $prop => $vlr) {
                $att = [];
                foreach ($vlr->attributes() as $a => $b) {
                    $att[$a] = (string)$b;
                }

                /*************** Class */
                $label = trim($vlr->rdfs_label);
                if (($label != '') and ($prop == 'owl_Class')) {
                    $prefix = 'brapci';
                    $ClassId = $RDFclass->register($prefix, $label);
                    $url[$att['rdf_about']] = $ClassId;
                }
            }

            /************ Proprierty ***********************************/
            $Property = [];
            foreach ($xml as $prop => $vlr) {
                /*************** Prop */
                $label = trim($vlr->rdfs_label);
                if (($label != '') and ($prop == 'owl_ObjectProperty')) {
                    $prefix = 'brapci';
                    $idP = $RDFproperty->register($prefix, $label);
                    $Property[$label] = $idP;
                }
            }

            /************ Domain and Range  ****************************/
            foreach ($xml as $prop => $vlr) {
                $ids = 0;
                $label = trim($vlr->rdfs_label);

                if ($prop == 'owl_ObjectProperty') {

                    /* Recupera URL do Label */
                    $type = trim((string)$vlr->rdfs_label);
                    /*************** Range */
                    foreach ($vlr->rdfs_range as $data => $p2) {
                        foreach ($p2->attributes() as $a => $b) {
                            $b = (string)$b;
                            $idu = $url[$b];
                            $idc = $Property[$label];
                            $RDFclassRange->register($idc, $idu);
                        }
                        /*************** Domain */
                        foreach ($vlr->rdfs_domain as $data => $p2) {
                            foreach ($p2->attributes() as $a => $b) {
                                $b = (string)$b;
                                $idu = $url[$b];
                                $idc = $Property[$label];
                                $RDFclassDomain->register($idc, $idu);
                            }
                        }
                    }
                }
            }
            echo "FIM da IMPORTAÇÂO";
            exit;
        }
    }

    function getNameSpace($nameSpace)
    {
        $RDFprefix = new \App\Models\RDF2\RDFprefix();
        foreach ($nameSpace as $prefix => $uri) {
            $RDFprefix->register($prefix, $uri);
        }
    }

    /*************************************************** ALL */
    function importRDFAll()
    {
        echo "GetAll<hr>";
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $dt = $RDFconcept
            ->join('brapci.rdf_concept as b_rdf', 'rdf_concept.id_cc = b_rdf.id_cc', 'right')
            ->where('rdf_concept.id_cc is null')
            ->findAll(500);

        foreach ($dt as $id => $line) {
            $RSP = $this->importRDF($line['id_cc']);
            echo $line['id_cc'] . ' ' . $line['cc_class'] . '<br>';
            if ($RSP['status'] != '200') {
                echo "ERRO";
                pre($RSP);
            }
        }
        echo "FIM";
        echo metarefresh('', 1);
    }

    function classConvert($class)
    {
        $c = [];
        $c['Journal'] = 'Journals';
        $c['ArticleSection'] = 'Section';
        $c['isPubishIn'] = 'isPubishOf';
        $c['ProceedingSection'] = 'Section';
        $c['Pages'] = 'Page';
        $c['Volume'] = 'PublicationVolume';
        $c['Author'] = 'Person';
        $c['Agent'] = 'Person';
        $c['fullText'] = '';
        if (isset($c[$class])) {
            $class = $c[$class];
        }
        return $class;
    }

    function propConvert($class)
    {
        $c = [];
        $c['dateOfPublication'] = 'wasPublicationInDate';
        $c['hasSummary'] = '';
        $c['hasVolume'] = '';

        if (isset($c[$class])) {
            $class = $c[$class];
        }
        return $class;
    }

    /*************************************************** */
    function importRDF($id)
    {
        $RSP = [];
        $RDF1 = new \App\Models\Rdf\RDF();
        $Volume = new \App\Models\AI\NLP\Text\Volume();
        $NumberVolume = new \App\Models\AI\NLP\Text\NumberVolume();

        $dt1 = $RDF1->le($id);

        if ($dt1 != []) {
            $class = $this->classConvert($dt1['concept']['c_class']);
            $dt1['concept']['c_class'] = $class;

            $RDF2 = new \App\Models\RDF2\RDF();
            $RDFconcept = new \App\Models\RDF2\RDFconcept();

            switch ($class) {
                case '':
                    //$RSP = $this->importGeneric($dt1);
                    $RSP['status'] = '200';
                    break;

                case 'Article':
                    $RSP = $this->importArticle($dt1);
                    break;
                case 'Book':
                    $RSP = $this->importBook($dt1);
                    break;
                case 'BookChapter':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'CDU':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'CDD':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'ClassificationAncib':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Collection':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'ContentType':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'CorporateBody':
                    $RSP = $this->importCorporateBody($dt1);
                    break;
                case 'Country':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Date':
                    $RSP = $this->importDate($dt1);
                    break;
                case 'DOI':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'ExclusiveDisjunction':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'File':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Gender':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Image':
                    $RSP = $this->importImage($dt1);
                    break;
                case 'ISBN':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'ISSN':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Issue':
                    $RSP = $this->importIssue($dt1);
                    break;
                case 'CnpqPQ':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'License':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Linguage':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Number':
                    $RSP = $this->importNumber($dt1);
                    break;
                case 'Page':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Person':
                    $RSP = $this->importPerson($dt1);
                    break;
                case 'Place':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'PublicationVolume':
                    $dt1['concept']['n_name'] = $Volume->normalize($dt1['concept']['n_name']);
                    $dt1['concept']['n_lang'] = 'nn';
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'PublicationNumber':
                    $dt1['concept']['n_name'] = $NumberVolume->normalize($dt1['concept']['n_name']);
                    $dt1['concept']['n_lang'] = 'nn';
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Publisher':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Journals':
                    $RSP = $this->importJournals($dt1);
                    break;
                case 'Proceeding':
                    $RSP = $this->importProceeding($dt1);
                    break;
                case 'RORID':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'SerieName':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Section':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Subject':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'FileStorage':
                    /* TO CHECK */
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'FileType':
                    /* TO CHECK */
                    $RSP = $this->importGeneric($dt1);
                    break;

                default:
                    $RSP['status'] = '510';
                    $RSP['message'] = $class . ' don´t have method';
                    if (isset($dt1['concept']['id_cc'])) {
                        $RSP['ID'] = $dt1['concept']['id_cc'];
                    } else {
                        $RSP['ID'] = 'Invalid ID';
                    }
            }
        }

        $RSP['time'] = date("Y-m-d H:i:s");
        return $RSP;
    }

    function createConcept($dt1)
    {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFclass = new \App\Models\RDF2\RDFclass();

        $d['ID'] = $dt1['concept']['id_cc'];
        $d['Class'] = $dt1['concept']['c_class'];
        $d['Name'] = $dt1['concept']['n_name'];
        $d['Lang'] = $dt1['concept']['n_lang'];
        $IDC = $RDFconcept->createConcept($d);
        if ($IDC < 0) {
            $RSP['status'] = '500';
            switch ($IDC) {
                case -1:
                    $RSP['message'] = 'Classe ' . $d['Class'] . ' não exite';
                    break;
                default:
                    $RSP['message'] = 'Erro não informado';
            }
        } else {
            $RSP['status'] = '200';
        }
        $RSP['Term'] = $dt1['concept']['n_name'] . '@' . $dt1['concept']['n_lang'];
        $RSP['ID'] = $IDC;
        $RSP['Class'] = $dt1['concept']['c_class'];
        return $RSP;
    }
    /************************************************ DATA */
    function importData($dt, $ID)
    {
        $RDF1 = new \App\Models\Rdf\RDF();
        $RDF2 = new \App\Models\RDF2\RDF();
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $RDFrules = new \App\Models\RDF2\RDFrules();
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $RDFliteral = new \App\Models\RDF2\RDFliteral();

        /**************************** DATAS */
        if (isset($dt['data'])) {
            $dados = $dt['data'];

            $validator = $RDFrules->validator($dt);

            if ($validator == true) {
                foreach ($dados as $id => $line) {
                    /************************* Propriedade */
                    $prop = trim($line['c_class']);
                    if ($prop != '') {
                        $prop = $this->propConvert($prop);
                        if ($prop != '') {
                            $id_prop = $RDFclass->getClass($prop);
                            if ($id_prop == 0) {
                                echo "OPS - Propriedade não existe $prop\n";
                            }
                            /********************** Dados das propriedades */
                            $lit = 0;
                            $ID2 = $line['d_r2'];
                            if ($ID2 == $ID) {
                                $ID2 = $line['d_r1'];
                            }

                            if ($line['d_literal'] == 0) {
                                echo "Registrar";
                                $RDFdata->register($ID, $id_prop, $ID2, $lit);
                                echo '==>' . $id_prop;
                                pre($line, false);
                            } else {
                                /*********************** Literal */
                                echo "Registrar Literal";
                                $name = $line['n_name'];
                                $lang = $line['n_lang'];
                                $ID2 = 0;
                                $lit = $RDFliteral->register($name, $lang);
                                $RDFdata->register($ID, $id_prop, $ID2, $lit);
                            }
                        }
                    } else {
                        $id_prop = 0;
                    }
                }
            } else {
                echo "Erro de Validação";
            }
        }
    }

    /********************************************* FIM DATA */

    function importDate($dt1)
    {
        /********** TO DO */
        $RSP['status'] = 200;
        $RSP = $this->createConcept($dt1);
        return $RSP;
    }

    function importImage($dt1)
    {
        /********** TO DO */
        $RSP = $this->createConcept($dt1);
        $RSP['data'] = $this->importData($dt1, $RSP['ID']);
        return $RSP;
    }

    function importIssue($dt1)
    {
        /********** TO DO */
        $RSP = $this->createConcept($dt1);
        return $RSP;
    }

    function importPerson($dt1)
    {
        $dt1['concept']['n_name'] = nbr_author($dt1['concept']['n_name'], 7);
        $dt1['concept']['n_lang'] = 'nn';
        $RSP = $this->createConcept($dt1);
        return $RSP;
    }

    function importCorporateBody($dt1)
    {
        $dt1['concept']['n_name'] = nbr_author($dt1['concept']['n_name'], 7);
        $dt1['concept']['n_lang'] = 'nn';
        $RSP = $this->createConcept($dt1);
        return $RSP;
    }

    function importJournals($dt1)
    {
        $dt['n_lang'] = 'nn';
        $RSP = $this->createConcept($dt1);
        return $RSP;
    }

    function importNumber($dt1)
    {
        $dt['n_lang'] = 'nn';
        $RSP = $this->createConcept($dt1);
        return $RSP;
    }

    function importGeneric($dt1)
    {
        $RSP = $this->createConcept($dt1);
        return $RSP;
    }

    function importProceeding($dt1)
    {
        $RSP = $this->createConcept($dt1);
        return $RSP;
    }

    function importBook($dt1)
    {
        $RSP = $this->createConcept($dt1);
        $RSP['data'] = $this->importData($dt1, $RSP['ID']);
        pre($RSP);

        return $RSP;
    }

    function importArticle($dt1)
    {
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $RSP = $this->createConcept($dt1);
        return $RSP;
    }
}
