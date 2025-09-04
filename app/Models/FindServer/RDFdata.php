<?php

namespace App\Models\FindServer;

use CodeIgniter\Model;

class RDFdata extends Model
{
    protected $DBGroup          = 'findserver';
    protected $table            = 'rdf_data';
    protected $primaryKey       = 'id_d';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'd_r1','d_r2','d_p','d_literal','d_o','d_user'
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

    function register($idc,$prop,$ida,$literal)
    {
        $RDFclass = new \App\Models\FindServer\RDFclass();
        $prop = $RDFclass->getClass($prop);

        if ($literal != '') {
            $RDFliteral = new \App\Models\FindServer\RDFliteral();
            $idliteral = $RDFliteral->getLiteral($literal,'pt_BR',true);
        } else {
            $idliteral = 0;
        }

        $dt = $this
            ->where('d_r1', $idc)
            ->where('d_p', $prop['id_c'])
            ->where('d_literal', $idliteral)
            ->where('d_r2', $ida)
            ->first();
        pre($dt);


        pre($prop);
        $data = [
            'id_cc' => $idc,
            'c_property' => $prop,
            'id_literal' => $idliteral,
            'id_authority' => $ida
        ];

        $r = $this
            ->where('id_cc', $idc)
            ->where('c_property', $prop)
            ->where('id_literal', $idliteral)
            ->where('id_authority', $ida)
            ->first();

        if (!isset($r['id'])) {
            $this->insert($data);
            $r = $this
                ->where('id_cc', $idc)
                ->where('c_property', $prop)
                ->where('id_literal', $idliteral)
                ->where('id_authority', $ida)
                ->first();
        }
        return $r['id'];
    }
}
