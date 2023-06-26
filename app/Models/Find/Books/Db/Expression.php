<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class Expression extends Model
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
        'id_be', 'be_title', 'be_isbn13',
        'be_isbn10', 'be_type', 'be_lang',
        'be_status', 'be_rdf','be_year',
        'be_authors','be_cover'
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

    function register_update($idx,$dtd)
        {
        /*
            $da['be_isbn13'] = $dtd['isbn13'];
            $da['be_isbn10'] = $dtd['isbn10'];
            $da['be_type'] = 1;
            $da['be_lang'] = $dtd['language'];
            $da['be_status'] = 1;
            $da['be_title'] = $dtd['title'];
            $da['be_rdf'] = $ide;
            $da['be_cover'] = $cover;
            */
            return $this->set($dtd)->where('be_rdf',$idx)->update();
        }

    function register($isbn,$dtd)
        {
            $RDF = new \App\Models\Find\Rdf\RDF();

            $name = 'ISBN:'.$isbn;
            $class = 'Expression';
            $ide = $RDF->concept($name,$class);
            echo $ide;
            exit;

            $dt = $this->where('be_rdf', $ide)->first();
            if ($dt == '')
                {
                    $this->set($dtd)->insert();
                    return 'Insert';
                } else {
                    $this->set($dtd)->where('id_be',$dt['id_be'])->update();
                    return 'Update';
                }
            return $ide;
        }
}
