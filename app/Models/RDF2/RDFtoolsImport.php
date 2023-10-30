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

    /*************************************************** */
    function inportRDF($id)
        {
            $RSP = [];
            $RDF1 = new \App\Models\Rdf\RDF();
            $dt1 = $RDF1->le($id);
            $class = $dt1['concept']['c_class'];

            $RDF2 = new \App\Models\RDF2\RDF();
            $RDFconcept = new \App\Models\RDF2\RDFconcept();



            switch($class)
                {
                    case 'Subject':
                        $RSP = $this->importSubject($dt1);
                        break;
                    case 'Article':
                    $RSP = $this->importArticle($dt1);
                    break;

                    default:
                        $RSP['status'] = '510';
                        $RSP['message'] = $class.' don´t have method';
                }
            $RSP['time'] = date("Y-m-d H:i:s");
            return $RSP;
        }

    function importSubject($dt1)
    {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFclass = new \App\Models\RDF2\RDFclass();

        $d['ID'] = $dt1['concept']['id_cc'];
        $d['Class'] = $dt1['concept']['c_class'];
        $d['Name'] = $dt1['concept']['n_name'];
        $d['Lang'] = $dt1['concept']['n_lang'];

        $IDC = $RDFconcept->createConcept($d);
        $RSP['Term'] = $dt1['concept']['n_name'].'@'. $dt1['concept']['n_lang'];
        $RSP['ID'] = $IDC;
        $RSP['Class'] = $dt1['concept']['c_class'];
        return $RSP;
    }

        function importArticle($dt1)
            {
                $RDFconcept = new \App\Models\RDF2\RDFconcept();
                $RDFclass = new \App\Models\RDF2\RDFclass();

                $d['ID'] = $dt1['concept']['id_cc'];
                $d['Class'] = $dt1['concept']['c_class'];
                $d['Name'] = $dt1['concept']['n_name'];
                $d['Lang'] = $dt1['concept']['n_lang'];

                $IDC = $RDFconcept->createConcept($d);

                /**************************** DATAS */
                if (isset($dt1['data']))
                {
                    $dados = $dt1['data'];
                    foreach($dados as $id=>$line)
                        {
                            $class = $line['c_class'];
                            $id_class = $RDFclass->getClass($class);
                            if ($id_class == 0)
                                {
                                    echo "OPS - Propriedade não existe <b>$class</b>";
                                }
                            echo '==>'.$id_class;

                            pre($line,false);
                        }
                }
            }
}
