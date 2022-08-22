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
        'b_source', 'b_rdf'
    ];

    protected $typeFields    = [
        'hidden', 'string:100', 'text*',
        'hidden', 'hidden'
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

    function createRDF($ids)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $ISBN = new \App\Models\Functions\Isbn();
        $TechinalProceessing = new \App\Models\Books\TechinalProceessing();

        $dt = $this->where('b_source', $ids)->findAll();
        $class = "Book";
        $name = $dt[0]['b_titulo'];
        $name = trim($name);
        $name = troca($name, chr(13), '');
        $name = troca($name, chr(10), '');

        $isbn = $dt[0]['b_isbn'];
        $isbn = $ISBN->format($isbn);
        $isbn10 = $ISBN->isbn13to10($isbn);

        /********************************** ARQUIVO */
        if ($dt[0]['b_rdf'] > 0)
            {
                $id = $dt[0]['b_rdf'];
            } else {
                $id = $RDF->RDF_concept('ISBN' . $isbn, $class);
            }

        /********************************* ISBNS */
        $dta = $TechinalProceessing->where('id_tp',$dt[0]['b_source'])->first();
        $fileO = $dta['tp_up'];
        $dir = $RDF->directory($id);
        $dir = troca($dir,'.c/','_repository/book/');
        checkdirs($dir);
        $fileD = $dir.'book.pdf';
        copy($fileO,$fileD);
        /********************************** ARQUIVO RDF */
        $id_file = $RDF->RDF_concept($fileD,'FileStorage');
        $prop = 'hasFileStorage';
        $RDF->propriety($id, $prop, $id_file, 0);

        /********************************** ISBN13 */


        $literal = $RDF->put_literal($isbn);
        $prop = 'hasISBN';
        $RDF->propriety($id, $prop, 0, $literal);

        $literal = $RDF->put_literal($isbn10);
        $prop = 'hasISBN';
        $RDF->propriety($id, $prop, 0, $literal);

        /********************************** ISBN10 */
        $literal = $RDF->put_literal($isbn10);
        $prop = 'hasISBN';
        $RDF->propriety($id, $prop, 0, $literal);

        /*********************************** API Consulta */
        $ISBN = new \App\Models\Functions\ISBNdb();
        $dt['metadata']['ISBNdb'] = $ISBN->_call($isbn);

        $ISBN = new \App\Models\Functions\MercadoEditorial();
        $dt['metadata']['MercadoEditorial'] = $ISBN->_call($isbn);

        /* Authors */
        $meta = $dt['metadata'];
        foreach ($meta as $source => $line) {
            if (isset($line['authors'])) {
                $auths = $line['authors'];
                for ($r = 0; $r < count($auths); $r++) {
                    $nome = nbr_author($auths[$r], 1);
                    $ida = $RDF->RDF_concept($nome, 'Person');
                    $prop = 'hasAuthor';
                    $RDF->propriety($id, $prop, $ida, 0);
                }
            }
        }

        /* Title */
        $meta = $dt['metadata'];
        foreach ($meta as $source => $line) {
            if (isset($line['title'])) {
                $name = $line['title'];
                $literal = $RDF->put_literal($name);
                $prop = 'hasTitle';
                $RDF->propriety($id, $prop, 0, $literal);
            }
        }

        /* Subject */
        $meta = $dt['metadata'];
        foreach ($meta as $source => $line) {
            if (isset($line['editora'])) {
                $auths = $line['editora'];
                $ida = $RDF->RDF_concept($nome, 'Subject');
                $prop = 'hasSubject';
                $RDF->propriety($id, $prop, $ida, 0);
            }
        }


        /* Editora */
        $meta = $dt['metadata'];
        foreach ($meta as $source => $line) {
            if ((isset($line['editora']))  and (strlen(trim($line['editora'])) > 0)) {
                $nome = trim($line['editora']);
                $ida = $RDF->RDF_concept($nome, 'Publisher');
                $prop = 'isPublisher';
                $RDF->propriety($id, $prop, $ida, 0);
            }

            if ((isset($line['published'])) and (strlen(trim($line['published'])) > 0)) {
                $nome = trim($line['published']);
                $ida = $RDF->RDF_concept($nome, 'Date');
                $prop = 'dateOfPublication';
                $RDF->propriety($id, $prop, $ida, 0);
            }

            if ((isset($line['lang'])) and (strlen(trim($line['lang'])) > 0)) {
                $nome = trim($line['lang']);
                $ida = $RDF->RDF_concept($nome, 'Linguage');
                $prop = 'hasLanguageExpression';
                $RDF->propriety($id, $prop, $ida, 0);
            }

            if ((isset($line['cdd'])) and (strlen(trim($line['cdd'])) > 0)) {
                $nome = trim($line['cdd']);
                $ida = $RDF->RDF_concept($nome, 'CDD');
                $prop = 'hasClassificationCDD';
                $RDF->propriety($id, $prop, $ida, 0);
            }

            if ((isset($line['cdu'])) and (strlen(trim($line['cdu'])) > 0)) {
                $nome = trim($line['cdu']);
                $ida = $RDF->RDF_concept($nome, 'CDU');
                $prop = 'hasClassificationCDU';
                $RDF->propriety($id, $prop, $ida, 0);
            }

            if ((isset($line['cover'])) and (strlen(trim($line['cover'])) > 0)) {
                $nome = trim($line['cover']);
                $ida = $RDF->RDF_concept($nome, 'Image');
                $prop = 'hasCover';
                $RDF->propriety($id, $prop, $ida, 0);
            }

            if ((isset($line['pages'])) and (strlen(trim($line['pages'])) > 0)) {
                $nome = trim($line['pages']);
                $nome = troca($nome, 'p.', '');
                $nome = troca($nome, 'pag.', '');
                $nome = troca($nome, 'pags.', '');
                $nome = trim($nome) . ' p.';

                $ida = $RDF->RDF_concept($nome, 'Pages');
                $prop = 'hasPage';
                $RDF->propriety($id, $prop, $ida, 0);
            }

            $dt['b_rdf'] = $id;
            $this->set($dt)->where('b_source',$ids)->update();
        }

        return $id;
    }

    function editRDF($id)
        {
            $dt = $this->where('b_source',$id)->findAll();
            $idr = $dt[0]['b_rdf'];

            $RDF = new \App\Models\Rdf\RDF();
            $sx = $RDF->form($idr);
            return $sx;
        }
}