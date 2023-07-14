<?php

namespace App\Models\Authority\API;

use CodeIgniter\Model;

class AuthName extends Model
{
    protected $DBGroup          = 'authority';
    protected $table            = 'auth_name';
    protected $primaryKey       = 'id_n';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_an','an_name', 'an_name_asc','an_lang'
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

    function register($name,$lang=1)
        {
            $name_asc = mb_strtoupper(ASCII($name));
            $dt = $this
                    ->where('an_name_asc', $name_asc)
                    ->first();
            if ($dt=='')
                {
                    $dt['an_name'] = $name;
                    $dt['an_name_asc'] = $name_asc;
                    $id = $this->set($dt)->insert();
                } else {
                    $id = $dt['id_an'];
                }
            return $id;
        }
}
