<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesKeywordsProducao extends Model
{
    protected $DBGroup          = 'lattes';
    protected $table            = 'lattes_keyword_producao';
    protected $primaryKey       = 'id_kp';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_kp', 'kp_keyword','kp_producao','kp_tipo'
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

    function register($prod, $key,$type)
        {
            $dt = $this
                ->where('kp_keyword',$key)
                ->where('kp_producao', $prod)
                ->where('kp_tipo',$type)
                ->first();
            if ($dt == '')
                {
                    $dt['kp_keyword'] = $key;
                    $dt['kp_producao'] = $prod;
                    $dt['kp_tipo'] = $type;
                    $this->set($dt)->insert();
                }
            return true;
        }
}
