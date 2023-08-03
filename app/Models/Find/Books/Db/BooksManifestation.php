<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class BooksManifestation extends Model
{
    protected $DBGroup          = 'find';
    protected $table            = 'rdf_data';
    protected $primaryKey       = 'id_d';
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

    function getData($id)
        {
            $cp = 'id_cc,id_c,c_prefix,c_class,n_name,n_lang,c_order';
            $dt = $this
                ->select($cp)
                ->join('rdf_class as prop','d_p = prop.id_c','LEFT')
                ->join('rdf_concept as concept','concept.id_cc = d_r2', 'LEFT')
                ->join('rdf_name','cc_pref_term = id_n', 'LEFT')
                ->where('d_r1',$id)
                ->orderBy('c_order, c_class')
                ->findAll();
            return $dt;
        }

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
                'isbn10' => 'ignore',
                'status' => 'ignore',
                'edicao' => 'Edition'
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
                'isbn10' => 'ignore',
                'status' => 'ignore',
                'edicao' => 'isEdition',
                'abstract'=> 'hasAbstract'
                ];


            if(!isset($property[$prop]))
                {
                    echo "<br>ERRO DE CLASSE - $prop";
                    exit;
                }
            /*********************************** Tipo */
            if ($property[$prop] != 'ignore')
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
