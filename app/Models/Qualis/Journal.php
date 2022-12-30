<?php

namespace App\Models\Qualis;

use CodeIgniter\Model;

class Journal extends Model
{
    protected $DBGroup          = 'capes';
    protected $table            = 'journals';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_j', 'j_name', 'j_issn', 'j_issn_l', 'j_country', 'updated_at'
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

    function register($dt)
        {
            $dta = $this->where('j_issn',$dt['j_issn'])->findAll();
            if (count($dta) == 0)
                {
                    $id = $this->set($dt)->insert();
                } else {
                    $id = $dta[0]['id_j'];
                }
            return $id;
        }
}
