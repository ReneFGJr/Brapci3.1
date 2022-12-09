<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Cover extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'downloads';
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

    function tumb($id,$dt)
        {
            $RDF = new \App\Models\Rdf\RDF();
            if ((isset($dt['n_name2'])) and (trim($dt['n_name2']) != ''))
                {
                    $img = trim($dt['n_name2']);
                }
            $img = $RDF->c($id);
            $place = $_SERVER['DOCUMENT_ROOT'] . '/'.$img;
            if (!file_exists($img))
                {
                    echo h($place);

                    pre($_SERVER);
                    $img = '/img/books/no_cover.png';
                }
            return $img;
        }

    function image($id='')
    {
        $RDF = new \App\Models\Rdf\RDF();
        $img = $RDF->c($id);
        return $img;
    }
}