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
        'id_be','be_title','be_authors',
        'be_year','be_cover','be_rdf',
        'be_isbn13','be_isbn10','be_type',
        'be_lang','be_status'
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

    function register($isbn,$library,$tombo, $user, $RSP)
        {
            $RSP['status'] = '200';
            $RSP['isbn'] = $isbn;
            $RSP['library'] = $library;
            $RSP['user'] = $user;
            $RSP['tombo'] = $tombo;

            /* Verifica se já existe na base */
            if (!$this->exists($isbn))
                {
                    $RSP['process'] = 'Novo registro';
                    $RSP = $this->processa_novo_item($RSP);
                } else {
                    $RSP['process'] = 'Novo item';
                }

            return $RSP;
        }

        function processa_novo_item($RSP)
            {
                /* Checar ISBNdb */
                $ISBNdb = new \App\Models\ISBN\Isbndb\Index();
                $djson = $ISBNdb->search($RSP['isbn']);
                $dt = (array)json_decode($djson);
                if (isset($dt['book']))
                    {
                        $dt = (array($dt['book']));
                        $dt = $ISBNdb->convert($dt);
                        $dt['status'] = 2;
                    } else {
                        $dt['title'] = '[Sem titulo localizado ISBN:'.$RSP['isbn'];
                        $dt['status'] = 1;
                    }
                return $this->registarItem($RSP,$dt);
            }

        function registarItem($RSP,$dt)
            {
                $titulo = $dt['title'];
                $Books = new \App\Models\Find\Books\Db\Books();
                $idt = $Books->register($titulo);

                $Lang = new \App\Models\AI\NLP\Language();
                $lg = $Lang->normalize($dt['language']);

                $authors = '';
                foreach($dt['authors'] as $id=>$nome)
                    {
                        if ($authors != '') { $authors .= '; ';}
                        $authors .= nbr_author($nome,7);
                    }

                if (isset($dt['date']))
                    {
                        $year = $dt['date'];
                    } else {
                        $year = 0;
                    }

                if (isset($dt['cover'])) {
                    $cover = $dt['cover'];
                } else {
                    $cover = PATH.'/img/cover/no_cover.png';
                }
                /********************************** Registra Recurso */
                $RDF = new \App\Models\Find\Rdf\RDF();
                $rdf = $RDF->concept('ISBN:'. $dt['isbn13'], 'Book');

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

                $dv = $this->where('be_isbn13',$dt['isbn13'])->findAll();
                if (count($dv) == 0)
                    {
                        $ide = $this->set($de)->insert();
                        $ide = 1;
                    } else {
                        $ide = $dv['id_be'];
                    }

                $BookManifestation = new \App\Models\Find\Books\Db\BooksManifestation();
                foreach($dt as $prop=>$reg)
                    {
                        $BookManifestation->register($rdf,$prop,$reg);
                    }

                /******************************** REGISTRA UM ITEM */
                $Books = new \App\Models\Find\Books\Db\BooksLibrary();
                $RSP['expression'] = $rdf;
                $RSP = $Books->register($RSP);
            }

        function exists($isbn)
            {
                $dt = $this->where('be_isbn13',$isbn)->first();
                return (!($dt == ''));
            }
}
