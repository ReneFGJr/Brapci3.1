<?php

namespace App\Models\keywords;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'keywords';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = [];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    function index($d1='', $caID='')
    {
        $RSP = [];
        $RSP['status'] = '404';
        $RSP['status_message'] = 'Function not Found';
        switch ($d1) {
            case 'get':
                $RSP = $this->getKeywords($caID);
                $RSP['status'] = '200';
                $RSP['status_message'] = 'OK';
                break;
            default:
                $RSP['status'] = '404';
                $RSP['status_message'] = 'Function '.$d1.' not Found';
                break;
        }
        return $RSP;
    }

    function getKeywords($d1)
    {
        $RSP = [];
        $RPS['status'] = '200';
        $idz = get("idz");
        $IDs = explode(",",$idz);
        $Search = new \App\Models\Elasticsearch\Search();
        $dt = $Search->whereIn('ID',$IDs)->findAll();
        $RSP['data'] = $dt;
        pre($dt);
        return $RSP;
    }
}
