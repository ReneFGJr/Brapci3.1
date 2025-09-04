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
            $idliteral = $idliteral['id_n'];
        } else {
            $idliteral = 0;
        }
        $prop = $prop['id_c'];

        $dt = $this
            ->where('d_r1', $idc)
            ->where('d_p', $prop)
            ->where('d_literal', $idliteral)
            ->where('d_r2', $ida)
            ->first();

        if (!isset($dt['id_d'])) {
            $data = [
                'd_r1' => $idc,
                'd_p' => $prop,
                'd_literal' => $idliteral,
                'd_r2' => $ida,
                'd_user' => 1
            ];
            $this->insert($data);
            $dt = $this
                ->where('d_r1', $idc)
                ->where('d_p', $prop)
                ->where('d_literal', $idliteral)
                ->where('d_r2', $ida)
                ->first();
        }
        return $dt['id_d'];
    }
}
