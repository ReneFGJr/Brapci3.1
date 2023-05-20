<?php

namespace App\Models\ControlledVocabulary;

use CodeIgniter\Model;

class ThesaurusDescriptors extends Model
{
    protected $DBGroup          = 'vc';
    protected $table            = 'thesaurus_descriptors';
    protected $primaryKey       = 'id_term';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_term ', 'term_name', 'term_name_asc',
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

    function register($term)
        {
            $dt = $this
                ->where('term_name',$term)
                ->first();

            if ($dt == '')
                {
                    $dt['term_name'] = $term;
                    $dt['term_name_asc'] = mb_strtolower(ascii($term));
                    $idt = $this->set($dt)->insert();
                } else {
                    $idt = $dt['id_term'];
                }
            return $idt;
        }
}
