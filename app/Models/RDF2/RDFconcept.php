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
        'id_cc','cc_class', 'cc_use', 'cc_pref_term',
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
            $cp = 'id_cc, cc_use, prefix_ref, c_class, n_name, n_lang, cc_status, cc_created, cc_update';
            //$cp = '*';
            pre($id,false);
            $dc = $this
                ->select($cp)
                ->join('rdf_literal', 'cc_pref_term = id_n','left')
                ->join('rdf_class', 'id_c = cc_class')
                ->join('rdf_prefix', 'id_prefix = c_prefix')
                ->where('id_cc',$id)
                ->first();

            /* Data */

            return $dc;
        }

    function createConcept($dt)
        {
            $RDFliteral = new \App\Models\RDF2\RDFliteral();
            $d = [];

            /* Literal Value */
            $d['cc_pref_term'] = $RDFliteral->register($dt['Name'],$dt['Lang']);

            /********************* Classe */
            $RDFclass = new \App\Models\RDF2\RDFclass();
            $d['cc_class'] = $RDFclass->getClass($dt['Class']);
            $d['cc_use'] = 0;
            $d['cc_origin'] = '';
            $d['cc_update'] = date("Y-m-d");
            $d['cc_status'] = 1;

            /* Verifica se existe a Classe */
            if ($d['cc_class'] <= 0) { return -1; }

            /* Verifica se já existe */
            $new = true;

            /********************* COM o ID */
            if (isset($dt['ID'])) {
                $d['id_cc'] = $dt['ID'];
                $dti = $this->find($dt['ID']);
                if ($dti != null)
                    {
                        $new = false;
                        $ID = $dti['id_cc'];
                    }
            }
            if ($new == true)
            {
                $ID = $this->set($d)->insert();
            } else {
                $this->set($d)->where('id_cc',$ID)->update();
            }
            return $ID;
        }

    function totalProp($class)
        {
            return 0;
        }
    function totalClass($class)
        {
            $RDFclass = new \App\Models\RDF2\RDFclass;
            if (sonumero($class) != $class)
                {
                    $class = $RDFclass->getClass($class);
                }
            $dt = $this
                ->select('count(*) as total')
                ->where('cc_class',$class)
                ->first();
            if ($dt == null)
                {
                    return 0;
                }
            return($dt['total']);
        }

    function getData($class)
        {
            $RDFconcept = new \App\Models\RDF2\RDFconcept();
            $RDFclass = new \App\Models\RDF2\RDFclass();
            $class = $RDFclass->getClass($class);

            $dt = $RDFconcept->getClassRegisters($class);
            return $dt;
        }

    function getClassRegisters($class)
        {
            $cp = 'id_cc as ID, cc_use as use, n_name as label, c_class as Class';
            $dt = $this
                ->select($cp)
                ->join('rdf_literal', 'cc_pref_term = id_n')
                ->join('rdf_class', 'cc_class = id_c')
                ->where('cc_class',$class)
                ->findAll();
            return $dt;
        }
}
