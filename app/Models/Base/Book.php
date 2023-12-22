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

    /*********** Funcoes da versão nova */
    function vitrine($q='')
        {
            $RDF2 = new \App\Models\RDF2\RDF();
            $RDF2metadata = new \App\Models\RDF2\RDFmetadata();
            $Class = 'Book';
            $dt = $RDF2->recoverClass($Class,$q,0,12,'desc');
            $d = [];

            foreach($dt as $n=>$dta)
                {
                    $id = $dta['id_cc'];
                    $dtm = $RDF2metadata->simpleMetadata($id);
                    array_push($d,$dtm);
                }
            return $d;
        }

    /*********** Legado */

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


    function v($id = '')
    {
        $sx = '';
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $RDF->le($id);

        if (!isset($dt['concept']['c_class']))
            {
                return view('errors/html/error_404');
            }
        $class = $dt['concept']['c_class'];

        switch ($class) {
            case 'Subject':
                $Keywords = new \App\Models\Base\Keywords();
                $sx .= $Keywords->showHTML($dt);
                break;

            case 'Book':
                $Book = new \App\Models\Base\Book();
                $sx = $Book->showFULL($id);
                break;

            case 'BookChapter':
                $Book = new \App\Models\Base\Book();
                $sx = $Book->showFULL($id);
                break;

            case 'ClassificationAncib':
                $ClassificationAncib = new \App\Models\Books\ClassificationAncib();
                $sx = $ClassificationAncib->showFULL($id);
                break;

            default:
                $sx .= h($class, 1);
                $sx .= h('view not exists',5);
                $sx = bs(bsc($sx));
                $sx .= $RDF->view_data($id);
                break;
        }
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