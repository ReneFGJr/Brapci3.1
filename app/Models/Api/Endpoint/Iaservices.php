<?php
/*
@category API
@package Brapci AI
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2022 CC-BY
@access public/private/apikey
@example $URL/api/ai/
@abstract API para consulta de metadados de livros com o ISBN
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Iaservices extends Model
{
    protected $DBGroup          = 'IA';
    protected $table            = 'links';
    protected $primaryKey       = 'id_lk';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_lk',
        'lk_name',
        'lk_logo',
        'lk_link',
        'lk_preco',
        'lk_update',
        'lk_decrição'
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

    function index($d1, $d2='', $d3='', $d4='')
    {
        $RSP = [];
        header('Access-Control-Allow-Origin: *');
        if (get("test") == '') {
            header("Content-Type: application/json");
        }

        switch ($d2) {
            case 'chat':
                $RSP = [];
                $dt = $this->findAll();
                echo json_encode($dt);
                exit;
                break;

            default:
                $RSP['status'] = '500';
                $RSP['message'] = 'Método não existe - ' . $d2;
                break;
        }
        echo json_encode($RSP);
        exit;
    }
}
