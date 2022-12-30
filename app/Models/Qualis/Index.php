<?php

namespace App\Models\Qualis;

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
            echo '==>'. $d1 . '=d2=' . $d2 . '=d3=' . $d3.']';
            switch($d1)
                {
                    case 'event':
                        $Evento = new \App\Models\Qualis\Evento();
                        $sx = bs(bsc($Evento->index($d2, $d3), 12));
                        break;
                    case 'area':
                        $Area = new \App\Models\Qualis\Areas();
                        $sx = bs(bsc($Area->index($d2,$d3),12));
                        break;
                    default:
                        $sx = $this->menu();
                }
            return $sx;
        }

        function menu()
            {
                $menu = array();
                $menu[PATH . COLLECTION . '/qualis/event'] = lang('brapci.qualis.event');
                $menu[PATH.COLLECTION.'/qualis/area'] = lang('brapci.qualis.area');
                return menu($menu);
            }
}
