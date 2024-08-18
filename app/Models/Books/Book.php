<?php

namespace App\Models\Books;

use CodeIgniter\Model;

class Book extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'books';
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

    function create_book($dt)
    {
        $isbn = sonumero($dt['b_isbn']);
        $isbn = 'ISBN' . $isbn;
        $RDF = new \App\Models\RDF2\RDF();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $RDFliteral = new \App\Models\RDF2\RDFliteral();
        $RDFproperty = new \App\Models\RDF2\RDFproperty();
        $Language = new \App\Models\AI\NLP\Language();

        $dd['Class'] = 'Book';
        $dd['Name'] = $isbn;
        $dd['Lang'] = 'nn';
        //$dd['Name'] = $dt['b_titulo'];
        //$dd['Lang'] = $Language->getTextLanguage($dd['Name']);
        $idc = $RDFconcept->createConcept($dd);

        /********************************** TITLE */
        $prop = 'hasTitle';
        $name = $dt['b_titulo'];
        $RDFconcept->registerLiteral($idc, $name, '', $prop);

        /********************************** LANGUAGE */
        $prop = 'hasLanguageExpression';
        $dd['Class'] = 'Language';
        $dd['Name'] = $Language->getTextLanguage($name);
        $dd['Lang'] = 'nn';
        $idl = $RDFconcept->createConcept($dd);
        $id_prop = $RDFproperty->getProperty($prop);
        $RDFdata->register($idc, $id_prop, $idl, 0);

        /********************************** FILE */
        $prop = 'hasFileStorage';
        $file = $this->directory($idc);
        $RDFconcept->registerLiteral($idc, $file, '', $prop);

        return $idc;
    }

    function directory($idc)
    {
        $file = strzero($idc, 8);
        $file = substr($file, 0, 2) . '/' . substr($file, 2, 2) . '/' . substr($file, 4, 2) . '/' . substr($file, 6, 2);
        $file = '_repository/book/' . $file;
        dircheck($file);
        $file .= '/book.pdf';
        return $file;
    }

    function registerChapter($IDBook, $txt)
    {
        $RDF = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $RDFliteral = new \App\Models\RDF2\RDFliteral();
        $Mark = new \App\Models\AI\NLP\Book\Sumary();
        $RSP['status'] = '201';

        $txt = get("text");
        $RSP = $Mark->processarTexto($txt);
        $TXT = [];
        foreach($RSP as $id=>$line)
            {
                $name = '[Chapter:'.$IDBook.strzero($id,3).']'.md5($line['TITLE'].$IDBook);
                $dt = [];
                $dt['Class'] = 'BookChapter';
                $dt['Name'] = $name;
                $dt['Lang'] = 'nn';
                $IDch = $RDF->createConcept($dt);
                $id_prop = 'hasBookChapter';
                $lit = 0;
                $RDFdata->register($IDBook, $id_prop, $IDch, $lit);

                /* TItle */
                $id_prop = 'hasTitle';
                $name = $line['TITLE'];
                $lang = $line['LANGUAGE'];
                $IDn = $RDFliteral->register($name,$lang);
                $RDFdata->register($IDch, $id_prop, 0, $IDn);

                $authors = $line['AUTHORS'];
                foreach($authors as $name)
                    {
                        $dt = [];
                        $dt['Class'] = 'Person';
                        $dt['Name'] = $name;
                        $dt['Lang'] = 'nn';
                        $IDauth = $RDF->createConcept($dt);

                        $id_prop = 'hasAuthor';
                        $RDFdata->register($IDch, $id_prop, $IDauth, 0);
                    }

                /* Abstract */
                $id_prop = 'hasAbstract';
                $name = $line['ABSTRACT'];
                $lang = $line['LANGUAGE'];
                $IDn = $RDFliteral->register($name, $lang);
                $RDFdata->register($IDch, $id_prop, 0, $IDn);

                /* Pagina */
                $pag = $line['PAGE_START'];
                if ($pag != '')
                {
                    $dt = [];
                    $dt['Class'] = 'Page';
                    $dt['Name'] = $pag;
                    $dt['Lang'] = 'nn';
                    $IDpagh = $RDF->createConcept($dt);

                    $id_prop = 'hasPageStart';
                    $RDFdata->register($IDch, $id_prop, $IDpagh, 0);
                }

                $pag = $line['PAGE_END'];
                if ($pag != '') {
                    $dt = [];
                    $dt['Class'] = 'Page';
                    $dt['Name'] = $pag;
                    $dt['Lang'] = 'nn';
                    $IDpagh = $RDF->createConcept($dt);

                    $id_prop = 'hasPageEnd';
                    $RDFdata->register($IDch, $id_prop, $IDpagh, 0);
                }

                array_push($TXT, ['book' => $IDBook, 'bookChap' => $IDch, 'status' => 'Created','Authors'=> $IDauth]);

            }
        return $TXT;
    }
}
