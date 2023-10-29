<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFconcept extends Model
{
    var $DBGroup                = 'rdf2';
    var $table                  = 'rdf_concept';
    protected $primaryKey       = 'id_cc';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_cc','cc_class', 'cc_use ', 'cc_pref_term ',
        'c_equivalent', 'cc_origin', 'cc_status',
        'cc_update', 'cc_origin'
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
            $dc = $this->find($id);
            return $dc;
        }

    function createConcept($dt)
        {
            $RDFliteral = new \App\Models\RDF2\RDFliteral();
            $d = [];
            /********************* Literal */
            if (isset($dt['ID']))
                {
                    $dc = $this->le($dt['ID']);
                    if ($dc != null)
                        {
                            return $dc['id_cc'];
                        }
                    $d['id_cc'] = $dt['ID'];
                    $ID = $dt['ID'];
                } else {
                    $dc = [];
                    $ID = 0;
                }

            if ($dc == null)
                {
                    $d['cc_pref_term'] = $RDFliteral->register($dt['Name'],$dt['Lang']);
                }

            /********************* Classe */
            $RDFclass = new \App\Models\RDF2\RDFclass();
            $d['cc_class'] = $RDFclass->getClass($dt['Class']);
            $d['cc_use'] = 0;
            $d['cc_origin'] = '';
            $d['cc_update'] = date("Y-m-d");
            $d['cc_status'] = 1;
            $ID = $this->set($d)->insert();
            return $ID;
        }
}
