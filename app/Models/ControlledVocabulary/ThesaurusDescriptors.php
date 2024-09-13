<?php

namespace App\Models\ControlledVocabulary;

use CodeIgniter\Model;

class ThesaurusDescriptors extends Model
{
    protected $DBGroup          = 'vc';
    protected $table            = 'thesa_concept';
    protected $primaryKey       = 'id_c';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_c',
        'c_thesa',
        'c_group',
        'c_term',
        'c_property',
        'c_brapci',
        'c_update'
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

    function le($id)
        {
            $dt = $this
                ->join('thesa_literal', 'c_term = id_l')
                ->where('c_group',$id)
                ->orderby('l_term')
                ->findAll();
            return $dt;
        }
}
