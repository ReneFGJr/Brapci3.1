<?php

namespace App\Models\Authority\API;

use CodeIgniter\Model;

class AuthResource extends Model
{
    protected $DBGroup          = 'authority';
    protected $table            = 'auth_resource';
    protected $primaryKey       = 'id_ar';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ar','an_url', 'an_concept','an_prop'
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

    function register($idc,$prop,$source)
        {
            $dt = $this
                ->where('an_concept',$idc)
                ->where('an_prop', $prop)
                ->where('an_url', $source)
                ->first();
            if ($dt == '')
                {
                    $dt['an_prop'] = $prop;
                    $dt['an_concept'] = $idc;
                    $dt['an_url'] = $source;
                    $this->set($dt)->insert();
                }
        }
}
