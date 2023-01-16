<?php

namespace App\Models\AI\Wiki;

use CodeIgniter\Model;

class Index extends Model
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

    function index($d1,$d2,$d3)
        {
            $sx = '';
            $Term = new \App\Models\AI\Wiki\Term();
            $sb = $this->submenu();
            switch($d1)
                {
                    case 'indice':
                        $sa = $Term->catalog();
                        break;
                    case 'inport':
                        $sa = $Term->Import();
                        break;

                    default:
                        $sa = $Term->Index();
                        $sb .= '<hr>'.$Term->btn_edit;
                        break;
                }

            return bs(bsc($sa,10).bsc($sb,2));
        }

        function submenu()
            {
                $menu = array();
                $menu['#TERMS'] = '';
                $menu[PATH.'/ai/wiki/inport/'] = lang('brapci.inport');
                $menu[PATH . '/ai/wiki/indice/'] = lang('brapci.indice');
                return menu($menu);
            }
}
