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

class Event extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'event';
    protected $primaryKey       = 'id_ev';
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

    function index($d1, $d2, $d3)
    {
        header('Access-Control-Allow-Origin: *');
        if (get("test") == '') {
            header("Content-Type: application/json");
        }
        switch ($d1) {
            default:
                $this->list($d2);
        }
    }

    function list($status=0)
        {
        $dt = date("Y-m-d");
        $dt = "2023-01-01";
        $cp = 'ev_name as name, ev_place as place, ';
        $cp .= 'ev_data_start as start, ev_data_end as end, ev_url as URL, ';
        $cp .= 'ev_image as logo';
        $dt = $this
            ->select($cp)
            ->where("ev_data_end >= '".$dt."'")
            ->orderby('ev_data_start')
            ->findAll();
        echo json_encode($dt);
        exit;
        }
}
