<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class BooksExpression extends Model
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
        'id_be', 'be_title', 'be_authors',
        'be_year', 'be_cover', 'be_rdf',
        'be_isbn13', 'be_isbn10', 'be_type',
        'be_lang', 'be_status'
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
    var $data = [];

    function existISBN($isbn)
        {
            $isbn = sonumero($isbn);
            $dt = $this->where('be_isbn13',$isbn)->first();
            return ($dt != '');
        }

    function registerEmpty($isbn)
        {
        $ISBN = new \App\Models\Functions\Isbn();
        $dt = [];
        $dt['title'] = '[Sem titulo localizado ISBN:' . $isbn . ']';
        $dt['isbn13'] = sonumero($isbn);
        $dt['isbn10'] = $ISBN->isbn13to10($isbn);
        $dt['status'] = '1';
        $RSP['status'] = '203';
        $RSP['isbn'] = $isbn;
        $RSP['message'] = 'ISBN Inserido com sucesso';
        return $this->register($RSP, $dt);
        }

    function register($RSP, $dt)
    {
        $titulo = $dt['title'];
        $RDF = new \App\Models\Find\Rdf\RDF();
        $Books = new \App\Models\Find\Books\Db\Books();
        $Authors = new \App\Models\Find\Books\Db\Authors();
        $idt = $Books->register($titulo);

        $Lang = new \App\Models\AI\NLP\Language();
        if (isset($dt['language'])) {
            $lg = $Lang->normalize($dt['language']);
        } else {
            $lg = 'pt_BR';
        }



        if (isset($dt['date'])) {
            $year = $dt['date'];
        } else {
            $year = 0;
        }

        if (isset($dt['cover'])) {
            $cover = $dt['cover'];
        } else {
            $cover = PATH . '/img/cover/no_cover.png';
        }
        /********************************** Registra Recurso */
        $RDF = new \App\Models\Find\Rdf\RDF();

        $rdf = $RDF->concept('ISBN:' . $dt['isbn13'], 'Book');

        /*********************************************** Authors */
        $authors = '';
        if (isset($dt['authors'])) {
            $prop = $RDF->class('hasAuthor');

            foreach ($dt['authors'] as $id => $nome) {
                if ($authors != '') {
                    $authors .= '; ';
                }
                $name = nbr_author($nome, 7);
                $authors .= $name;
                $ida = $Authors->register($name);
                $RDF->prop($rdf, $prop, $ida, 0);
            }
        }

        /********************************** Registra Expression */
        $de = [];
        $de['be_title'] = $idt;
        $de['be_authors'] = $authors;
        $de['be_year'] = $year;
        $de['be_cover'] = $cover;
        $de['be_rdf'] = $rdf;
        $de['be_isbn13'] = $dt['isbn13'];
        $de['be_isbn10'] = $dt['isbn10'];
        $de['be_type'] = 1;
        $de['be_lang'] = 1;
        $de['be_status'] = $dt['status'];

        $dv = $this->where('be_isbn13', $dt['isbn13'])->findAll();
        if (count($dv) == 0) {
            $ide = $this->set($de)->insert();
            $ide = 1;
        }

        $BookManifestation = new \App\Models\Find\Books\Db\BooksManifestation();
        foreach ($dt as $prop => $reg) {
            $BookManifestation->register($rdf, $prop, $reg);
        }


        if (isset($dt['authors'])) {


        }

        $RSP['status'] = '205';
        return $RSP;
    }

    function getISBN($isbn)
        {
            $isbn = sonumero($isbn);
            $dt = $this
                    ->join('books', 'be_title = id_bk')
                    ->where('be_isbn13', $isbn)
                    ->first();

            if ($dt != '')
                {
                    $BooksResponsability = new \App\Models\Find\Books\Db\Authors();
                    $dt['authors'] = $BooksResponsability->getResposability($dt['be_rdf']);

                    $BooksManifestation = new \App\Models\Find\Books\Db\BooksManifestation();
                    $dt['data'] = $BooksManifestation->getData($dt['be_rdf']);
                } else {
                    $dt = [];
                }
            return $dt;
        }

    function exists($isbn)
    {
        $dt = $this->where('be_isbn13', $isbn)->first();
        $this->data = $dt;
        return (!($dt == ''));
    }
}
