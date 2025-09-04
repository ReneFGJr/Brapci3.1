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
        'd_r1',
        'd_r2',
        'd_p',
        'd_literal',
        'd_o',
        'd_user'
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

    function le($id)
    {

        $cp = 'd_r2 as ID, P.c_class as property, C2.c_class as Class, L2.n_name as literal, L2.n_lang as lang';
        $dt1 = $this
            ->select($cp)
            ->join('rdf_concept R1', 'd_r1 = R1.id_cc', 'left')
            ->join('rdf_class C1', 'R1.cc_class = C1.id_c', 'left')
            ->join('rdf_literal L1', 'd_literal = L1.id_n', 'left')
            ->join('rdf_class P', 'd_p = P.id_c', 'left')
            ->join('rdf_concept R2', 'd_r2 = R2.id_cc', 'left')
            ->join('rdf_class C2', 'R2.cc_class = C2.id_c', 'left')
            ->join('rdf_literal L2', 'R2.cc_pref_term = L2.id_n', 'left')
            ->where('d_r1', $id)
            ->where('d_r2 !=', 0)
            ->findAll();

        $dt2 = $this
            ->select($cp)
            ->join('rdf_concept R1', 'd_r1 = R1.id_cc', 'left')
            ->join('rdf_class C1', 'R1.cc_class = C1.id_c', 'left')
            ->join('rdf_literal L1', 'd_literal = L1.id_n', 'left')
            ->join('rdf_class P', 'd_p = P.id_c', 'left')
            ->join('rdf_concept R2', 'd_r2 = R2.id_cc', 'left')
            ->join('rdf_class C2', 'R2.cc_class = C2.id_c', 'left')
            ->join('rdf_literal L2', 'R2.cc_pref_term = L2.id_n', 'left')
            ->where('d_r2', $id)
            ->findAll();

        $cp = 'd_r2 as ID, P.c_class as property, "Literal" as Class, L2.n_name as literal, L2.n_lang as lang';
        $dt3 = $this
            ->select($cp)
            ->join('rdf_concept R1', 'd_r1 = R1.id_cc', 'left')
            ->join('rdf_class C1', 'R1.cc_class = C1.id_c', 'left')
            ->join('rdf_literal L1', 'd_literal = L1.id_n', 'left')
            ->join('rdf_class P', 'd_p = P.id_c', 'left')
            ->join('rdf_literal L2', 'd_literal = L2.id_n', 'left')
            ->where('d_r1', $id)
            ->where('d_literal !=', 0)
            ->findAll();
        return array_merge($dt1, $dt2, $dt3);
    }



    function register($idc, $prop, $ida, $literal)
    {
        $RDFclass = new \App\Models\FindServer\RDFclass();
        $prop = $RDFclass->getClass($prop);
        $prop = $prop['id_c'];

        if ($ida == '0') {
            $ida == 0;
        }
        $literal = trim($literal);
        if (($literal == '0') or ($literal == 0)) {
            $idliteral = 0;
        } else {
            if ($literal != '') {
                $RDFliteral = new \App\Models\FindServer\RDFliteral();

                $lit = $RDFliteral->getLiteral($literal, 'pt_BR', true);

                if (!isset($lit['id_n'])) {
                    echo "Erro na criação do literal $literal<br>";
                    pre($lit);
                }
                $idliteral = $lit['id_n'];
            }
        }

        /* Verifica se existe */
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
