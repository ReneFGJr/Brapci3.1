<?php
/*
@category API
@package Brapci OAI-PMH Server
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2024 CC-BY
@access public/private/apikey
@example $URL/api/oaiserver/list
@abstract API para consulta de metadados servidores OAI-PMH Local
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Oaiservereditor extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'oaiserver';
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
        $RSP = [];
        header('Access-Control-Allow-Origin: *');
        if (get("test") == '') {
            header("Content-Type: application/json");
        }
        switch ($d1) {
            case 'repository':
                $OaiServer = new \App\Models\OaiServer\Index();
                $dt = $OaiServer->le($d2);
                echo json_encode($dt);
                exit;
                break;

            case 'listidentifiers':
                $ListRecords = new \App\Models\OaiServer\ListRecords();
                $dt = $ListRecords->list($d2);
                echo json_encode($dt);
                exit;
                break;

            default:
                $OaiServer = new \App\Models\OaiServer\Index();
                $dt = $OaiServer->list();
                echo json_encode($dt);
                exit;
                break;
        }
        echo json_encode($RSP);
        exit;
    }
}
