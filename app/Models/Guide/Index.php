<?php

namespace App\Models\Guide;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
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

    function index($d1='',$d2='',$d3='')
        {

            $sx = '';

            switch($d1)
                {
                    case 'popup':
                        $Course = new \App\Models\Guide\Course\Index();
                        $sx .= $Course->index($d2, $d3);
                        break;
                    case 'course':
                        $Course = new \App\Models\Guide\Course\Index();
                        $sx .= $Course->index($d2,$d3);
                        break;
                    default:
                        $menu = [];
                        $menu[PATH.'/guide/course/'] = lang('brapci.course');
                        $sx .= menu($menu);
                        $sx = bs(bsc($sx));
                }


            return $sx;
        }
}
