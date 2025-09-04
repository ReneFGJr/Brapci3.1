<?php

namespace App\Models\FindServer;

use CodeIgniter\Model;

class RDFconcept extends Model
{
    protected $DBGroup          = 'findserver';
    protected $table            = 'rdf_concept';
    protected $primaryKey       = 'id_cc';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_cc','cc_class','cc_use','cc_pref_term','cc_origin','cc_status','cc_library','c_equivalent'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

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

    function createConcept($class, $name, $lang = 'pt_BR')
    {
        $RDFclass = new \App\Models\FindServer\RDFclass();
        $cl = $RDFclass->getClass($class);
        if (isset($cl['id_c'])) {
            $id_class = $cl['id_c'];
        } else {
            return 0;
        }

        $RDFliteral = new \App\Models\FindServer\RDFliteral();
        $lt = $RDFliteral->getLiteral($name, $lang, true);

        $dt = $this->where('cc_class', $id_class)
            ->where('cc_pref_term', $lt['id_n'])
            ->first();
        if ($dt == null || !isset($dt['id_cc'])) {
            $data = [
                'cc_class'     => $id_class,
                'cc_use'       => 0,
                'cc_pref_term' => $lt['id_n'],
                'cc_origin'    => 'import',
                'cc_status'    => 1,
                'cc_library'   => 1,
                'c_equivalent'=> ''
            ];
            $this->insert($data);
            $dt = $this->where('cc_class', $id_class)
                ->where('cc_pref_term', $lt['id_n'])
                ->first();
        }
        return $dt['id_cc'];
    }

}
