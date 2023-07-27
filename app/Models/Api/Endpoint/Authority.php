<?php
/*
@category API
@package Authority
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2023 CC-BY
@access public/private/apikey
@example $URL/api/authority/services
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Authority extends Model
{
    protected $DBGroup          = 'authority';
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
            header('Access-Control-Allow-Origin: *');
            $Auth = new \App\Models\Authority\API\Index();
            $RSP = [];
            $RSP['status'] = '200';
            switch($d1)
                {
                    case 'getid':
                        $RSP['data'] = $Auth->getid($d2);
                        break;
                    case 'search':
                        $RSP['data'] = $Auth->search($d2,$d3);
                        break;
                    case 'put':
                        $name = nbr_author($d2,7);
                        $RSP['id'] = $Auth->register($name);
                        $RSP['person'] = $name;
                        $RSP['uri'] = 'https://hdl.handle.net/20.500.11959/person/' . $RSP['id'];
                        break;
                    case 'cpf':
                        $cpf = $d2;
                        if ($cpf == '')
                            {
                                $cpf = get("cpf");
                            }                        
                        $cpf = sonumero($cpf);
                        $RSP['valid'] = false;
                        $RSP['exist'] = false;
                        if (validaCPF($cpf))
                            {
                                $auth = new \App\Models\Authority\API\Index();
                                $dt = $auth->getCPF($cpf);
                                $dt['cpf'] = substr($cpf,0,3).'.'.substr($cpf,3,3).'.'.substr($cpf,6,3).'-'.substr($cpf,8,2);
                                $RSP['valid'] = true;
                                $RSP['data'] = $dt;
                                if (isset($dt['data']['an_name']))
                                    {
                                        $RSP['exist'] = true;
                                    }
                                
                                
                            }
                        $RSP['cpf'] = $cpf;
                        break;
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
                $srv = ['search','put','cpf'];
                $RSP['services'] = $srv;
                return $RSP;
            }
}
