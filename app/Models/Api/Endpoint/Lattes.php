<?php

namespace App\Models\Api\Endpoint;
/*
@category API
@package Brapci Lattes
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2023 CC-BY
@access public/private/apikey
@example https://brapci.inf.br/api/lattes/convert/K2999994T9
@abstract API para uso do Lattes
*/

use CodeIgniter\Model;

class Lattes extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'lattes';
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
            switch($d1)
                {
                    case 'convert':
                        $API = new \App\Models\Api\Lattes\Index();
                        $API->convert_KtoN($d2);
                        exit;
                }
        }
}