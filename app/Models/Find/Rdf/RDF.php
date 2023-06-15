<?php

namespace App\Models\Find\Rdf;

use CodeIgniter\Model;

class RDF extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rdfs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

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

    function concept($idc, $idn)
        {
            $RDFConcept = new \App\Models\Rdf\RDFConcept();
            $RDFConcept->table = 'find.' . $RDFConcept->table;
            $dt = $RDFConcept
                ->where('cc_class',$idc)
                ->where('cc_pref_term',$idn)
                ->first();
            if ($dt == '')
                {
                    $dt['cc_class'] = $idc;
                    $dt['cc_user'] = 0;
                    $dt['cc_created'] = date("Y-m-dTH:i:s");
                    $dt['cc_pref_term'] = $idn;
                    $dt['cc_origin'] = '';
                    $dt['cc_update'] = date("Y-m-dTH:i:s");
                    $dt['cc_status'] = 1;
                    $dt['cc_library'] = 0;
                    $dt['c_equivalent'] = 0;
                    $idc = $RDFConcept->set($dt)->insert();
                } else {
                    $idc = $dt['id_cc'];
                }
            return $idc;
        }

    function class($class)
        {
        $RDFClass = new \App\Models\Rdf\RDFClass();
        $RDFClass->table = 'find.' . $RDFClass->table;

        $idc = $RDFClass->class($class);
        return $idc;
        }

    function literal($name,$lg='pt-BR', $force=1)
    {
        $RDFLiteral = new \App\Models\Rdf\RDFLiteral();
        $RDFLiteral->table = 'find.' . $RDFLiteral->table;

        $idn = $RDFLiteral->name($name, $lg, $force);
        return $idn;
    }
}
