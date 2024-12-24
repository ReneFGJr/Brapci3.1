<?php

namespace App\Models\FindServer;

use CodeIgniter\Model;

class API extends Model
{
    protected $table            = 'apis';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

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

    function index($act='',$d1='',$d2='')
        {
            switch($act)
                {
                    case 'searchISBN':
                        $RSP = $this->searchISBN($d1);
                        break;
                    default:
                        $RSP['message'] = 'Verb not found';
                        break;
                }
            echo json_encode($RSP);
            exit;
        }

    function searchISBN($ID)
        {
            $Catalog = new \App\Models\FindServer\Catalog();
            $RSP = [];
            $ISBN = new \App\Models\ISBN\Index();
            $RSP['ISBN'] = $ISBN->isbns($ID);
            $RSP['data'] = $Catalog->findISBN($RSP['ISBN']);

            return $ID;
        }
}
