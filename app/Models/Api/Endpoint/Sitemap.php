<?php
/*
@category API
@package Brapci SiteMap - ISC
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2024 CC-BY

@example $PATH/api/sitemap/create
@example $PATH/api/sitemap/download
@abstract Criar SiteMap.XML
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Sitemap extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = '*';
    protected $primaryKey       = '*';
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

    function index($d1='',$d2='')
        {
            $Sitemap = new \App\Models\Sitemap\Index();
            switch($d1)
                {
                    case 'create':
                        $Sitemap->create();
                        $RSP = [];
                        $RSP['status'] = '200';
                        $RSP['message'] = 'Sitemap created';
                        $RSP['url'] = PATH.'/sitemap.xml';
                        break;
                    default:
                        $txt = file_get_contents('sitemap.xml');
                        echo $txt;
                        exit;
                }

            echo json_encode($RSP);
            exit;
        }



}
