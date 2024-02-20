<?php
/*
@category API
@package KANBAN
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2024 CC-BY
@access public/private/apikey
@example $URL/api/kanban/apikey
@abstract API Consuta atividades de Usuário (Kanban)
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Kanban extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'kanbans';
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

    function index($d1, $d2)
    {
        header('Access-Control-Allow-Origin: *');
        if (get("test") == '') {
            header("Content-Type: application/json");
        }

        $Kanban = new \App\Models\Kanban\Index();
        $key = get("apikey");
        $key = 'ff63a314d1ddd425517550f446e4175e';
        $RSP = [];
        $RSP = $Kanban->get_schedule($key);
        echo json_encode($RSP);
        exit;
    }
}
