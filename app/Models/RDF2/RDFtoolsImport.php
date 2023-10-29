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
}
