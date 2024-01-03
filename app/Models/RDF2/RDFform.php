<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFform extends Model
{
    protected $DBGroup          = 'rdf2';
    protected $table            = 'rdf_class_domain';
    protected $primaryKey       = 'id_cd';
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

    function index($act,$id,$d3)
        {
            $sx = '';
            switch($act)
                {
                    case 'editRDF':
                        $sx .= $this->editRDF($id,$d3);
                        break;
                }
            return $sx;
        }
    function editRDF($id,$d3)
        {
            $RDF = new \App\Models\RDF2\RDF();
            $RDFclass = new \App\Models\RDF2\RDFclass();

            $dt = $RDF->le($id);

            $Class = $dt['concept']['c_class'];
            $idc = $RDFclass->getClass($Class);

            $cp = 'rdf_class1.c_class as Class1';
            $cp .= ', rdf_class2.c_class as Class2';

            $dt = $this
                ->select($cp)
                ->join('rdf_class as rdf_class1', 'rdf_class1.id_c = cd_property')
                ->join('rdf_class_range', 'cr_property = cd_property')
                ->join('rdf_class as rdf_class2', 'rdf_class2.id_c = cr_range')
                ->where('cd_domain',$idc)
                ->orderBy('Class1,Class2')
                ->findAll();

            pre($dt);
        }
}
