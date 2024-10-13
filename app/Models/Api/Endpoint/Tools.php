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

class Tools extends Model
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
        $Net = new \App\Models\Tools\Net\Index();

        switch($d2)
            {
                case 'monitor':
                    header('Access-Control-Allow-Origin: *');
                    header("Content-Type: application/json");
                    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
                    header("Access-Control-Allow-Headers: Content-Type, Authorization");
                    $Monitor = new \App\Models\Monitor\Index();
                    $RSP = $Monitor->checkIP();
                    echo json_encode($RSP);
                    exit;
                case 'mark':
                    $Mark = new \App\Models\AI\NLP\Book\Sumary();
                    $txt = get("text");
                    $Mark->markup($txt);
                    break;
                case 'txt4matrix':
                    $Net->index($d1, $d2, $d3, $d4);
                    exit;
                    break;
                case 'txt4net':
                    $Net->index($d1, $d2, $d3, $d4);
                    exit;
                    break;
                case 'txt4unit':
                    $Net->index($d1, $d2, $d3, $d4);
                    exit;
                    break;
                case 'txt4unit2':
                    $Net->index($d1, $d2, $d3, $d4);
                    exit;
                    break;
                case 'ris4marc':
                    header('Access-Control-Allow-Origin: *');
                    header("Content-Type: application/json");
                    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
                    header("Access-Control-Allow-Headers: Content-Type, Authorization");

                    $text = get("text");
                    $RIS = new \App\Models\Metadata\RIS();
                    $RSP = [];
                    $RSP['response'] = $RIS->risToMarc21($text);
                    $RSP['RIS'] = $text;
                    $RSP['status'] = '200';

                    //$RSP['post'] = $_POST;
                    echo json_encode($RSP);
                    exit;
                break;
            }
    }
}