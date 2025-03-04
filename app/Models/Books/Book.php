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
        $RDF = new \App\Models\RDF2\RDF();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $RDFliteral = new \App\Models\RDF2\RDFliteral();
        $RDFproperty = new \App\Models\RDF2\RDFproperty();
        $Language = new \App\Models\AI\NLP\Language();

        $dd['Class'] = 'Book';
        $dd['Name'] = $dt['b_isbn'];
        $dd['Lang'] = 'nn';
        //$dd['Name'] = $dt['b_titulo'];
        //$dd['Lang'] = $Language->getTextLanguage($dd['Name']);
        $idc = $RDFconcept->createConcept($dd);


        /********************************** FILE */
        $file = $dt['fileO'];
        $fileO = "../.tmp/booksubmit/" . $file;
        $prop = 'hasFileStorage';
        $dd['Class'] = 'FileStorage';
        $dd['Name'] = $file;
        $dd['Lang'] = 'nn';
        $idfile = $RDFconcept->createConcept($dd);

        $file = $this->directory($idfile);
        $prop = 'hasFileType';
        $prop = 'hasFileSize';
        $size = filesize($fileO);
        $RDFconcept->registerLiteral($idfile, $size, '', $prop);

        $prop = 'hasFileStorage';
        $place = $file;
        $RDFdata->registerLiteral($idfile, $place, '', $prop);

        $prop = 'prefLabel';
        $prop = 'hasDateTime';



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
        $RDF2 = new \App\Models\RDF2\RDF();
        $RDF = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $RDFliteral = new \App\Models\RDF2\RDFliteral();
        $Mark = new \App\Models\AI\NLP\Book\Sumary();
        $RSP['status'] = '201';

        $BOOK = $RDF2->le($IDBook);
        $file = $RDF2->extract($BOOK, 'hasFileStorage','A');
        if (isset($file[0]))
            {
                $file = $file[0];
            } else {
                $file = '';
            }

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

                /* Arquivo */
                if ($file != '')
                    {
                        $id_prop = 'hasFileStorage';
                        $RDFdata->register($IDch, $id_prop, $file, 0);
                    }

                /* TItle */
                $id_prop = 'hasTitle';
                $name = $line['TITLE'];
                $lang = $line['LANGUAGE'];
                $IDn = $RDFliteral->register($name,$lang);
                $RDFdata->register($IDch, $id_prop, 0, $IDn);

                $authors = $line['AUTHORS'];
                foreach($authors as $name)
                    {
                        if (trim($name) != '')
                        {
                        $dt = [];
                        $dt['Class'] = 'Person';
                        $dt['Name'] = $name;
                        $dt['Lang'] = 'nn';
                        $IDauth = $RDF->createConcept($dt);

                        $id_prop = 'hasAuthor';
                        $RDFdata->register($IDch, $id_prop, $IDauth, 0);
                        }
                    }

                /* Abstract */
                $id_prop = 'hasAbstract';
                $name = $line['ABSTRACT'];
                $lang = $line['LANGUAGE'];
                $IDn = $RDFliteral->register($name, $lang);
                $RDFdata->register($IDch, $id_prop, 0, $IDn);

                /* Subject */
                $keyword = $line['KEYWORD'];
                foreach ($keyword as $name) {
                    if (trim($name) != '') {
                        $dt = [];
                        $dt['Class'] = 'Subject';
                        $dt['Name'] = $name;
                        $dt['Lang'] = 'nn';
                        $IDauth = $RDF->createConcept($dt);

                        $id_prop = 'hasSubject';
                        $RDFdata->register($IDch, $id_prop, $IDauth, 0);
                    }
                }

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
