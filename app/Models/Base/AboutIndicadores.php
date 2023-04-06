<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class AboutIndicadores extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'cms_indicador';
    protected $primaryKey       = 'id_cmsi';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_cmsi', 'cmsi_indicador', 'cmsi_valor'
    ];

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


    function makeIndicators()
        {
            $Search = new \App\Models\ElasticSearch\Register();
            $dt = $this
                ->findAll();
            pre($dt);
            break;
        }
}
