<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class UserApi extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'users';
    protected $primaryKey       = 'id_us';
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
    var $user = [];

    function checkUser()
        {
            $apikey = trim(get("apikey"));
            if ($apikey == '')
                {
                    $RSP['status'] = '202';
                    $RSP['message'] = 'APIKEY não foi informada';
                    return $RSP;
                } else {
                    if ($this->checkapi($apikey))
                        {
                            $this->getUser($apikey);
                            $RSP['status'] = '200';
                        } else {
                            $RSP['status'] = '202';
                            $RSP['message'] = 'Falha na autenticação APIKEY - '.$apikey;
                        }
                }
            return $RSP;
        }

    function getUser($apikey)
        {
             $dt = $this->where('us_apikey', $apikey)->first();
             $this->user = $dt;
        }

    function checkapi($apikey)
        {
            $apikey = trim($apikey);
            if ($apikey == '')
                {
                    return false;
                }

            $dt = $this->where('us_apikey',$apikey)->findAll();
            if (count($dt) == 0)
                {
                    return false;
                }

            return true;
        }
}
