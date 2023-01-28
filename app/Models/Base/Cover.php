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
            $place = $img;
            if (substr($img,0,4) == 'http')
                {
                    $place = troca($img,PATH,'');
                    if (substr($place,0,1) == '/')
                        {
                            $place = substr($place,1,strlen($place));
                        }
                } else {
                    $place = $img;
                }

            if (!file_exists($place))
                {
                    $img = '/img/books/no_cover.png';
                }
            return $img;
        }

    function image($id='')
    {
        $RDF = new \App\Models\Rdf\RDF();
        $img = $RDF->c($id);
        $img_chk = troca($img,URL,'');
        if (!file_exists($img_chk))
            {
                echo $img_chk;
                $img = '/img/thema/image_broke.svg';
            }
        return $img;
    }
}