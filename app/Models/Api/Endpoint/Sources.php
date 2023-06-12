<?php
/*
@category API
@package Brapci Sources
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2022 CC-BY
@access public/private/apikey
@example $PATH/api/source/
@abstract Mostra todas as fontes indexadas na Brapci, Parametros:<ul><li>Fontes: <a href="$PATH/api/source/">$PATH/api/source/</a></li><li>Coleções: <a href="$PATH/api/source/collections">$PATH/api/source/collections</a></li></ul>
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Sources extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'source_source';
    protected $primaryKey       = 'id_jnl';
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
        $type = get("type");


        switch($d1)
            {
                case 'collections':
                    return $this->collections($d2,$d3);
                    break;
                default:
                    return $this->all();
            }
    }

    function collections($d1,$d2)
        {
            header('Access-Control-Allow-Origin: *');
            header("Content-type: application/json; charset=utf-8");
            $Collections = new \App\Models\Base\Collections();

            echo $Collections->list('json');
            exit;


        }

    function all()
        {
            header('Access-Control-Allow-Origin: *');
            header("Content-type: application/json; charset=utf-8");

            $Sources = new \App\Models\Base\Sources();

            echo $Sources->list('json');
            exit;
        }

}
