<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFrules extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rdfrules';
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

    public $message = '';

    function RDFremissive()
        {
            $RDFdata = new \App\Models\RDF2\RDFdata();
            $dt = $RDFdata
                ->join('rdf_class','id_c = d_p')
                ->where('c_equivalent > 0')
                ->findAll();
                echo $RDFdata->getlastquery();
            pre($dt);
            exit;

        }

    function validator($dt)
    {
        $RDF2 = new \App\Models\RDF2\RDF();
        $ok = true;
        if (!$this->rule01($dt)) {
            $ok = false;
        }
        if (($ok == true) and (!$this->rule02($dt))) {
            $ok = false;
        }

        if ($ok == false) {
            pre($this->message);
        }

        return $ok;
    }
    function rule01($dt)
    {
        $RDF2 = new \App\Models\RDF2\RDF();
        $ok = false;

        if (isset($dt['concept']['id_cc'])) {
            $ok = $RDF2->valid($dt['concept']['id_cc']);
        } else {
            $this->message = 'Conceito não localizado';
        }
        return $ok;
    }
    function rule02($dt)
    {
        $RDF2 = new \App\Models\RDF2\RDF();
        $ok = true;
        if (isset($dt['concept']['id_cc'])) {
            $ID = $dt['concept']['id_cc'];
            if (isset($dt['data'])) {
                $data = $dt['data'];
                foreach ($data as $id => $line) {
                    $ID2 = $line['d_r2'];
                    if ($ID == $ID2)
                        {
                            $ID2 = $line['d_r1'];
                        }

                    /******************** Literal */
                    if (($line['d_r2'] == 0) and ($line['d_literal'] > 0))
                        {

                        } else {
                            if (!$RDF2->valid($ID2)) {
                                $ok = false;
                                $this->message = 'Conceito Destino não localizado ['.$ID2.']';
                            }
                        }
                }
            }
        }
        return $ok;
    }
}
