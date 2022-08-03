<?php

namespace App\Models\Books;

use CodeIgniter\Model;

class TechinalProceessingBook extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_books.books';
    protected $primaryKey       = 'id_b';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_b', 'b_isbn', 'b_titulo',
        'b_source'
    ];

    protected $typeFields    = [
        'hidden', 'string:100', 'text*',
        'hidden'
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

    function create($i)
    {
        $dt = $this->where('b_source', $i)->findAll();
        if (count($dt) == 0) {
            $dt['b_source'] = $i;
            $this->set($dt)->insert();
        }
    }

    function edit($id)
    {
        $dt = $this->where('b_source', $id)->findAll();
        $this->id = $dt[0]['id_b'];
        $this->path = PATH . COLLECTION . '/admin/auto/' . $id;
        $this->path_back = PATH . COLLECTION . '/admin/auto/' . $id;
        $sx = form($this);
        return $sx;
    }

    function createRDF($id)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $this->where('b_source', $id)->findAll();
        $class = "Book";
        $name = $dt[0]['b_titulo'];
        $name = trim($name);
        $name = troca($name, chr(13), '');
        $name = troca($name, chr(10), '');

        $isbn = $dt[0]['b_isbn'];
        $id = $RDF->RDF_concept('ISBN' . $isbn, $class);
        $literal = $RDF->put_literal($name);
        $prop = 'hasTitle';
        $RDF->propriety($id, $prop, 0, $literal);
        return $id;
    }
}