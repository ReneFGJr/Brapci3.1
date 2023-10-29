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
        $RDF = new \App\Models\RDF2\RDF();
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $RSP = [];
        //header("Content-Type: application/json");
        switch($d2)
            {
                case 'in':
                    $RDFtools = new \App\Models\RDF2\RDFtoolsImport();
                    $RSP = $RDFtools->inportRDF($d3);
                    break;
                default:
                    $RSP = $RDFclass->getClasses();
            }
        echo json_encode($RSP);
    }
}