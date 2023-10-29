<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFclassRange extends Model
{
    protected $DBGroup          = 'rdf2';
    protected $table            = 'rdf_class_range';
    protected $primaryKey       = 'id_cr';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cr_property', 'cr_range'
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

    function register($class,$range)
        {
            $this->where('cr_property',$class);
            $this->where('cr_range',$range);
            $dt = $this->first();
            if ($dt == null)
                {
                    $d = [];
                    $d['cr_property'] = $class;
                    $d['cr_range'] = $range;
                    return $this->set($d)->insert();
                } else {
                    return $dt['id_cr'];
                }
        }

        function listRange($id)
        {
            $dt = $this
                ->join('rdf_class', 'cr_range = id_c')
                ->where('cr_property', $id)
                ->findAll();
            return $dt;
        }
}
