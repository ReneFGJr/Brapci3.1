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

    function taxonomy()
        {
            $Sections = new \App\Models\Books\Sections();

            $menu = array();
            $menu[PATH.'/books/sections/'] = 'Sections';

            $sx = '
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <a class="navbar-brand" href="#">#</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link dropdown-toggle" href="#"
                                id="btn_category" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        ' . lang('brapci.category') . '
                        </a>
                    </li>
        </nav>';
        $sx .= $Sections->sections();
        return $sx;
;
        }

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
            $max = 4 * 4;
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
        $Work = new \App\Models\Base\Work();

        $dt = $RDF->le($id);
        $dd['book'] = $dt['data'];
        $sx = $Work->show($id);
        //$sx = view('Books/book', $dd);
        return $sx;
    }

    function show_reference($id)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $sx = $RDF->c($id) . cr();
        return $sx;
    }
}