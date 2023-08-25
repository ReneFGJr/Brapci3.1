<?php
/*
@category API
@package Handle.net
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2023 CC-BY
@access public/private/apikey
@example $PATH/api/handle/create/
@example $PATH/api/handle/update/
@example $PATH/api/handle/detele/
@abstract API para determinar o genero da pessoa pelo Nome <br>param: apikey, handle, url
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Handle extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'genre';
    protected $primaryKey       = 'id_gn';
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

    function index($d1 = '', $d2 = '', $d3 = '')
    {
        $apikey = get("apikey");
        $apikey = get("apikey");
        $apikey = get("apikey");
        if ($name != '') {
            return $this->getGenere($name);
        }
        return "NnN";

        $Genere = new \App\Models\Authority\Genere();
    }

}
