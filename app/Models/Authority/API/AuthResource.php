<?php

namespace App\Models\Authority\API;

use CodeIgniter\Model;

class AuthResource extends Model
{
    protected $DBGroup          = 'authority';
    protected $table            = 'authresources';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ar','an_url','an_name','an_prop'
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

    function register($idn,$source)
        {
            $dt = $this
                ->where('an_name',$idn)
                ->where('an_name', $idn)
                ->first();
            if ($dt == '')
                {
                    $dt['an_url'] = $source;
                    $dt['an_name'] = $idn;
                    $this->set($dt)->insert();
                }

        }
}
