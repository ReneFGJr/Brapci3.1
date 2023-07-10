<?php
/*
@category API
@package Brapci
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2023 CC-BY
@access public/private/apikey
@example $URL/api/brapci/services
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Brapci extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'finds';
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

    function index($d1,$d2,$d3)
        {
            $RSP = [];
            $RSP['status'] = '200';
            switch($d1)
                {
                    default:
                        $RSP = $this->services($RSP);
                        break;
                }
            echo json_encode($RSP);
            exit;
        }

        function services($RSP)
            {
                $srv = [];
                $srv['livros'] = ['name'=>'Livros','link'=>'books','icone'=>'icone'];
                $RSP['services'] = $srv;
                return $RSP;
            }
}
