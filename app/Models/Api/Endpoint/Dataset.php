<?php
/*
@category API
@package Datasets
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2024 CC-BY
@access public/private/apikey
@example $URL/api/brapci/datasets
@example $URL/api/brapci/datasets/all/authors

*/

namespace App\Models\API\Endpoint;
//https://brcris.ibict.br/vivo/individual/pers_c888cc57-bd54-4db4-b1ff-b91bccf83b2d/pers_c888cc57-bd54-4db4-b1ff-b91bccf83b2d.rdf

use CodeIgniter\Model;

class Dataset extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = '*';
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

    function index($d1,$d2='',$d3='')
        {
            $RSP = [];
            switch($d1)
                {
                    case 'full':
                        $RSP = $this->full($d2,$d3);
                        break;
                }
            echo json_encode($RSP);
            exit;
        }

    function full($d1,$d2)
        {
            $RSP = [];
            switch($d1)
                {
                    case 'authors':
                        $RSP = $this->full_authors('',$d2);
                        break;
                }
            return $RSP;
        }

    function full_authors($q,$type='csv')
        {
            $RSP = [];
            $Elastic= new \App\Models\ElasticSearch\Register();
            $file = '.tmp/brapci_'.date("Ymd-His").'.'.$type;

            $dt = $Elastic
                ->select('AUTHORS')
                ->findAll();

            $txt = '';
            foreach($dt as $id=>$line)
                {
                    $name = trim($line['AUTHORS']);
                    $name = troca($name, '(Org.)','');
                    $name = troca($name, '; ', ';');
                    $name = troca($name, '  ', ' ');
                    $name = ascii($name);
                    if ($name != '')
                        {
                            $txt .= $name . cr();
                        }

                }
            file_put_contents($file,$txt);
            $RSP['export'] = PATH.$file;
            return $RSP;
        }
}
