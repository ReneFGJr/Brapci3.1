<?php

namespace App\Models\Base\Admin;

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
    protected $allowedFields        = [];

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

    function index($act = '', $subact = '', $id = '')
    {
        switch ($act) {
            case 'source':
                $Sources = new \App\Models\Base\Sources();
                echo '===>' . $subact;
                switch ($subact) {
                    case 'edit':
                        echo '===>' . $id;
                        $sx = $Sources->editar($id);
                        break;
                    default:
                        $sx = $Sources->tableview();
                        break;
                }

                break;
            default:
                $sx = h($act, 1);
                $sx .= $this->menu();
                $sx = bs(bsc($sx, 12));
        }
        return $sx;
    }

    function menu()
    {
        $m[PATH .  COLLECTION . '/source'] =  lang('brapci.sources');
        $sx = menu($m);
        return $sx;
    }
}