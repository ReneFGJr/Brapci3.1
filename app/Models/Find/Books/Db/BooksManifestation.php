<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class BooksManifestation extends Model
{
    protected $DBGroup          = 'find';
    protected $table            = 'books_manifestation';
    protected $primaryKey       = 'id_bm';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bm ', 'bm_book_expression', 'bm_propriety', 'bm_resource', 'bm_literal'
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

    function register($resource_1,$prop,$valor)
        {
            $RDF = new \App\Models\Find\Rdf\RDF();

            $class = [
                'editora'=> 'Publisher',
                'language'=> 'Linguage',
                'cover'=>'Image',
                'title_long'=>'ignore',
                'dimensoes'=>'ignore',
                'pages'=>'Pages',
                'date'=>'Date',
                'authors' => 'Person',
                'title'=>'ignore',
                'isbn13' => 'ignore',
                'isbn10' => 'ignore'
                ];
            $property = [
                'editora'=> 'isPublisher',
                'language'=>'hasLanguageExpression',
                'cover'=> 'hasCover',
                'title_long' => 'ignore',
                'dimensoes' => 'ignore',
                'pages' => 'hasPage',
                'date' => 'dateOfPublication',
                'authors'=> 'hasAuthor',
                'title'=>'ignore',
                'isbn13'=>'ignore',
                'isbn10' => 'ignore'
                ];
            $type = [
                'editora' => 'C',
                'language' => 'C',
                'cover'=> 'F',
                'title_long' => 'I',
                'dimensoes' => 'I',
                'pages' => 'C',
                'date' => 'C',
                'authors' => 'C',
                'title'=>'I',
                'isbn13' => 'I',
                'isbn10' => 'I',
                'status'=>'I',
                ];

            if(isset($type[$prop]))
                {

                } else {
                    echo "<br>ERRO - $prop";
                    exit;
                }
            /*********************************** Tipo */
            if ($type[$prop] == 'C')
                {
                    if (!is_array($valor)) {
                        $valor=array($valor);
                    }
                    foreach($valor as $idv=>$content)
                        {
                            $literal = 0;
                            $content = (string)$content;
                            $resource_2 = $RDF->concept($content, $class[$prop]);
                            $RDF->prop($resource_1, $property[$prop], $resource_2, $literal);
                        }
                }

        }
    function registerReg($ide,$prop,$valor)
        {

        }
}
