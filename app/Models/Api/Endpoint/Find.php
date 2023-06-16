<?php
/*
@category API
@package Find
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2023 CC-BY
@access public/private/apikey
@example $URL/api/find/libraries/
@example $URL/api/find/lastitens/
@abstract $URL/api/find/libraries/ -> Lista as bibliotecas do Sistema
@abstract API para consulta de metadados de livros com o ISBN
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Find extends Model
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

    function lastitens()
        {
            $Books = new \App\Models\Find\Books\Db\Books();
            $dt = $Books->lastItens();
            pre($dt);
        }

    function index($d1,$d2,$d3)
        {
            header('Access-Control-Allow-Origin: *');
            switch($d1)
                {
                    case  'lastitens':
                        echo $this->lastItens();
                        exit;
                    break;

                    case 'libraries':
                        $Library = new \App\Models\Find\Library\Index();
                        $dt = $Library->listAll();
                        echo json_encode($dt);
                        exit;
                    break;
                }
        }
}
