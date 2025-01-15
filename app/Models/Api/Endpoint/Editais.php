<?php
/*
@category API
@package Eventos
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2024 CC-BY
@access public
@example $URL/api/event/
@abstract API para consulta de eventos ativos na área
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Editais extends Model
{
    protected $DBGroup          = 'editais';
    protected $table            = 'editais';
    protected $primaryKey       = 'id_e';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ev_count', 'id_ev'
    ];

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

    function index($d1, $d2, $d3)
    {
        $Editais = new \App\Models\Editais\Index();

        header('Access-Control-Allow-Origin: *');
        if (get("test") == '') {
            header("Content-Type: application/json");
        }

        switch ($d1) {
            case 'open':
                $RSP = $Editais->openEditais();
                break;
        echo json_encode($RSP);
        exit;
    }
}
