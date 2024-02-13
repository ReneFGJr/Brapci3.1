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
            $isbn = 'ISBN'.$isbn;
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
            $RDFconcept->registerLiteral($idc,$name,'',$prop);

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
                $file = substr($file,0,2).'/'.substr($file,2,2).'/'.substr($file,4,2).'/'.substr($file,6,2);
                $file = '_repository/book/'.$file.'/book.pdf';
                dircheck($file);
                return $file;
            }
}
