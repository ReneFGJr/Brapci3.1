<?php
/*
@category API
@package Eventos - Gev3nt
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2022 CC-BY
@access public/private/apikey
@example $PATH/api/Eveg3nt/events
@abstract API Controle de eventos e certificados
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Gev3nt extends Model
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
        header('Access-Control-Allow-Origin: *');
        $RSP = [];
        $RSP['status'] = '200';
        switch($d1)
            {
                default:
                $RSP = $this->services($RSP);
                break;
            }        
    }
    function services($RSP)
    {
        $srv = [];
        $srv = ['events'];
        $RSP['services'] = $srv;
        return $RSP;
    }    
}
   