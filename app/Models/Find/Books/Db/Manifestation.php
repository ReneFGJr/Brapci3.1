<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class Manifestation extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'manifestations';
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


    function register($ide, $dt)
    {
        foreach ($dt as $prop => $vlr) {
            switch ($prop) {
                case 'language':
                    break;
                case 'title_long':
                    break;
                case 'dimensoes':
                    break;
                case 'title':
                    break;
                case 'cover':
                    $id_img = $this->image($ide, $vlr, $dt['isbn13']);
                    break;
                case 'isbn13':
                    break;
                case 'isbn10':
                    break;
                case 'date':
                    $this->single($ide, $vlr, 'Date', 'isPubishIn');
                    break;
                case 'editora':
                    $this->editoras($ide, $vlr);
                    break;
                case 'authors':
                    $this->authors($ide, $vlr);
                    break;
                default:
                    echo h($prop);
            }
        }
    }

    function single($ide, $name , $class, $hasClass, $lang = 'NnN')
        {
            $RDF = new \App\Models\Find\Rdf\RDF();
            $idc = $RDF->concept($name, $class, $lang);
            $prop = $RDF->class($hasClass);
            $RDF->prop($ide, $prop, $idc, 0);
        }

    function image($ide,$vlr,$isbn)
        {
            $RDF = new \App\Models\Find\Rdf\RDF();
            if (substr($vlr,0,4) == 'http')
                {
                    $content = read_link($vlr);
                    if ($content != '')
                        {
                            $dir = '_repository/isbn/';
                            dircheck($dir);
                            $filename = $isbn.'.png';
                            file_put_contents($dir . $filename,$content);
                            $ext = pathinfo($filename, PATHINFO_EXTENSION);

                            /*********** Imagem Conceito */
                            $id_img = $RDF->concept($filename, 'Image' , $lang = 'NnN');

                            /*********** Propriedades da Imagem */
                            $prop = $RDF->class('hasFileName');
                            $literal = $RDF->literal($filename);
                            $RDF->prop($id_img, $prop, 0, $literal);

                            $prop = $RDF->class('hasFileSize');
                            $literal = filesize($dir.$filename);
                            $literal = $RDF->literal($literal);
                            $RDF->prop($id_img, $prop, 0, $literal);

                            $prop = $RDF->class('hasFileStorage');
                            $literal = $dir . $filename;
                            $literal = $RDF->literal($literal);
                            $RDF->prop($id_img, $prop, 0, $literal);

                            $prop = $RDF->class('hasFileType');
                            $literal = $ext;
                            $literal = $RDF->literal($literal);
                            $RDF->prop($id_img, $prop, 0, $literal);

                            $prop = $RDF->class('hasCover');
                            $RDF->prop($ide, $prop, $id_img, 0);
                        }
                }
        }

    function editoras($ide, $vlr)
    {
        $RDF = new \App\Models\Find\Rdf\RDF();

        $class = "brapci:Publisher";
        $hasClass = "brapci:isPublisher";
        $prop = $RDF->class($hasClass);

        foreach ($vlr as $id => $name) {
            $lang = 'NnN';
            $idc = $RDF->concept($name, $class, $lang);
            $RDF->prop($ide, $prop, $idc, 0);
        }
    }

    function authors($ide, $vlr)
    {
        $RDF = new \App\Models\Find\Rdf\RDF();

        $class = "brapci:Author";
        $hasClass = "brapci:hasAuthor";
        $prop = $RDF->class($hasClass);

        foreach ($vlr as $id => $name) {
            $lang = 'NnN';
            $idc = $RDF->concept($name, $class, $lang);
            $RDF->prop($ide, $prop, $idc, 0);
        }
    }
}
