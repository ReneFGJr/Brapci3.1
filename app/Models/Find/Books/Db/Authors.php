<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class Authors extends Model
{
    protected $DBGroup          = 'find';
    protected $table            = 'books_expression';
    protected $primaryKey       = 'id_be';
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
            $idc = $RDF->concept($name, $class);
            return $idc;
        }

    function getResposability($isbn)
        {
            $cp = 'n_name, n_lock, id_au, au_person, au_propriety, au_expression, au_order, c_class as propriery';
            $dt = $this
                ->select($cp)
                ->join('rdf_concept', 'au_person = id_cc')
                ->join('rdf_name', 'cc_pref_term = id_n')
                ->join('rdf_class', 'id_c = au_propriety')
                ->where('be_isbn13',$isbn)
                ->findAll();

            return $dt;
        }
}
