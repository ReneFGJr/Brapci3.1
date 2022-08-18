<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Book extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'work';
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

    function latest_acquisitions()
        {
            $RDF = new \App\Models\Rdf\RDF();
            $class = $RDF->getClass('Book');

            $RDFConcept = new \App\Models\Rdf\RDFConcept();
            $dt = $RDFConcept
                ->select('id_cc')
                ->where('cc_class',$class)
                ->orderBy('id_cc desc')
                ->findAll();

            $sx = '';
            $max = 12;
            if (count($dt) < $max) { $max = count($dt); }
            for ($r=0;$r < $max;$r++)
                {
                    $line = $dt[$r];
                    $sa = $this->show($line['id_cc']);
                    $sx .= bsc($sa,4,'border border-secondaty');
                }
            $sx = bs($sx);
            return $sx;
        }


    function showHTML($dt)
    {
        echo "ok";
        exit;
        $sx = view('RDF/work', $dt);
        return $sx;
    }

    function show($id)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $RDF->le($id);
        $dd['book'] = $dt['data'];
        $sx = view('Books/book_mini', $dd);
        return $sx;
    }

    function showFULL($id)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $RDF->le($id);
        $dd['book'] = $dt['data'];
        $sx = view('Books/book', $dd);
        return $sx;
    }

    function show_reference($id)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $sx = $RDF->c($id) . cr();
        return $sx;
    }
}