<?php
/*
@category API
@package Brapci PDF Tools
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2022 CC-BY
@access public/private/apikey
@example $URL/api/pdf/pdf_to_text/ <br>$data ['file'] = 'file.pdf';
@abstract API para consulta de metadados de livros com o ISBN
*/

namespace App\Models\Api\Endpoint;

use App\Models\RDF\RDFClass;
use CodeIgniter\Model;

class Rdf extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = '*';
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

    function index($d1, $d2, $d3, $d4)
    {
        /* NAO USADO PARA AS APIS */
        header('Access-Control-Allow-Origin: *');


        if (get("test") == '')
        {
            if (($d2 != 'import') and ($d2 != 'in') and ($d2 != 'searchSelect'))
            {
                header("Access-Control-Allow-Headers: Content-Type");
                header("Content-Type: application/json");
            }
        }

        $RDF = new \App\Models\RDF2\RDF();
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFform = new \App\Models\RDF2\RDFform();
        $RDFrules = new \App\Models\RDF2\RDFrules();
        $RDFclassDomain = new \App\Models\RDF2\RDFclassDomain();
        $RDFproperty = new \App\Models\RDF2\RDFproperty();
        $RSP = [];
        //header("Content-Type: application/json");
        switch($d2)
            {
                case 'createLiteral':
                    $dd = [];
                    $dd['post'] = $_POST;
                    echo json_encode($dd);
                    exit;
                    break;
                case 'createConcept':
                    $RDFconcept = new \App\Models\RDF2\RDFconcept();
                    $Language = new \App\Models\AI\NLP\Language();
                    $dd = [];
                    $dd['Class'] = $d3;
                    $dd['Name'] = get("name");
                    $dd['Lang'] = $Language->getTextLanguage($dd['Name']);
                    $dd['id'] = $RDFconcept->createConcept($dd);
                    echo json_encode($dd);
                    exit;
                case 'getResource':
                    $RSP = [];
                    $RSP['ID'] = get("ID");
                    $RSP['prop'] = get("prop");
                    $dt = $RDF->le($RSP['ID']);
                    $ClassID = $dt['concept']['id_c'];
                    $RSP['ClassID'] = $ClassID;
                    $propID = $RDFproperty->getProperty($RSP['prop']);
                    $RSP['resource'] = $RDFclassDomain->getResources($ClassID,$propID);
                    break;
                case 'dataAddLiteral':
                    /************************ REGISTRA */
                    $RDFClass = new \App\Models\RDF2\RDFclass();
                    $RDFdata = new \App\Models\RDF2\RDFdata();
                    $RDFliteral = new \App\Models\RDF2\RDFliteral();
                    $Language = new \App\Models\AI\NLP\Language();
                    $Literal = get("txt");
                    $lang = $Language->getTextLanguage($Literal);

                    $id_prop = $RDFClass->getClass(get("prop"));
                    $ID = get("source");
                    $ID2 = 0;
                    $lit = $RDFliteral->register($Literal,$lang);

                    $RDFdata->register($ID, $id_prop, $ID2, $lit);
                    break;
                case 'dataAdd':
                    /************************ REGISTRA */
                    $RDFClass = new \App\Models\RDF2\RDFclass();
                    $RDFdata = new \App\Models\RDF2\RDFdata();
                    $ID = get("source");
                    $id_prop = $RDFClass->getClass(get("prop"));
                    $ID2 = get("resource");
                    $lit = 0;
                    $RDFdata->register($ID, $id_prop, $ID2, $lit);
                    $RSP = $RDFform->searchSelect('', '');
                    break;
                case 'searchSelect':
                    $RSP = $RDFform->searchSelect($d3,$d4);
                    break;
                case 'search':
                    $RSP = $RDFform->search($d3,$d4);
                    break;
                case 'remissive':
                    $RSP['data'] = $RDFrules->RDFremissive();
                    break;
                case 'getdata':
                    $RSP['data'] = $RDFconcept->getData($d3);
                    break;
                case 'get':
                    $RDFClass = new \App\Models\RDF2\RDFclass();
                    $RSP = $RDFClass->get($d2);
                    break;
                case 'v':
                    $RSP = $RDF->v($d3);
                    break;
                case 'import':
                    $RDF = new \App\Models\RDF2\RDF();
                    $RSP = $RDF->import();
                    break;
                case 'resume':
                    $RDF = new \App\Models\RDF2\RDF();
                    $RSP = $RDF->resume();
                case 'in':
                    $RDFtools = new \App\Models\RDF2\RDFtoolsImport();
                    if ($d3 == 'all')
                        {
                            $RSP = $RDFtools->importRDFAll();
                        } elseif ($d3 == 'old')
                        {
                            $RSP = $RDFtools->importRDFOld();
                        } else {
                            $RSP = $RDFtools->importRDF($d3,true);
                        }
                    break;
                default:
                    $RSP = $RDFclass->getClasses();
                    break;
            }
        echo json_encode($RSP);
        exit;
    }
}