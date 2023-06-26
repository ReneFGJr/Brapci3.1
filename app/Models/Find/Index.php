<?php

namespace App\Models\Find;

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

    function index($d1,$d2,$d3)
        {
            echo "OK ------$d1";
            $sx = anchor(PATH. '/admin/find/inport','Inport FIND');
            switch($d1)
                {
                    case 'harvesting':
                        $BooksOld = new \App\Models\Find\BooksOld\Index();
                        $sx .= '<hr>';
                        $sx .= $BooksOld->harvesting($d2);
                        break;
                    case 'inport':
                        $BooksOld = new \App\Models\Find\BooksOld\Index();
                        $sx .= '<hr>';
                        $sx .= $BooksOld->inport();
                        $sx .= metarefresh(PATH.'admin/find/inport');
                        break;
                }
            return $sx;
        }
}
