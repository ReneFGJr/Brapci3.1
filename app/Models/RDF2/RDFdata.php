<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFdata extends Model
{
    protected $DBGroup          = 'rdf2';
    protected $table            = 'rdf_data';
    protected $primaryKey       = 'id_d';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'd_r1','d_r2','d_p','d_literal','d_ativo'
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

    function register($ID, $id_prop, $ID2, $lit)
        {
            $d = [];
            $d['d_r1'] = $ID;
            $d['d_r2'] = $ID2;
            $d['d_p'] = $id_prop;
            $d['d_literal'] = $lit;

            $dt = $this
                ->where('d_r1',$ID)
                ->where('d_r2', $ID2)
                ->where('d_p', $id_prop)
                ->where('d_literal', $lit)
                ->first();
            if ($dt == null)
                {
                    $this->set($d)->insert();
                    echo "NOVO";
                } else {

                }
        }
}
