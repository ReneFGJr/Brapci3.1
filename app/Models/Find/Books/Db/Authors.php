<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class Authors extends Model
{
    protected $DBGroup          = 'find';
    protected $table            = 'rdf_data';
    protected $primaryKey       = 'id_d';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_au', 'au_expression', 'au_propriety',
        'au_person', 'au_type', 'au_order'
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

    function register($name,$type="Author")
        {
            $class = "Person";
            $RDF = new \App\Models\Find\Rdf\RDF();
            if (strlen(trim($name)) > 2)
                {
                    $idc = $RDF->concept($name, $class);
                } else {
                    $idc = 0;
                }

            return $idc;
        }

    function getResposability($id,$class= 'hasAuthor')
        {
            $cp = 'id_cc,id_c,c_prefix,c_class,n_name,n_lang,c_order';
            $dt = $this
                ->select($cp)
                ->join('rdf_class as prop', 'd_p = prop.id_c', 'LEFT')
                ->join('rdf_concept as concept', 'concept.id_cc = d_r2', 'LEFT')
                ->join('rdf_name', 'cc_pref_term = id_n', 'LEFT')
                ->where('d_r1', $id)
                ->where('c_class', $class)
                ->orderBy('c_order, c_class')
                ->findAll();
            return $dt;
        }
}
