<?php

namespace App\Models\Thesa;

use CodeIgniter\Model;

class TermTh extends Model
{
    protected $DBGroup          = 'thesa';
    protected $table            = 'thesa_terms_th';
    protected $primaryKey       = 'thesa_terms_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'term_th_id', 'term_th_thesa', 'term_th_term',
        'term_th_concept'
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

    function asign($th,$term)
        {
            $dd['term_th_thesa'] = $th;
            $dd['term_th_term'] = $term;
            $dt = $this
                ->where('term_th_thesa',$th)
                ->where('term_th_term', $term)
                ->first();

            if ($dt == '')
                {
                    $this->set($dd)->insert();
                }
        }
}
