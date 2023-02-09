<?php

namespace App\Models\Dataverse;

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

    function logo($type='')
        {
            $type = lowercase(substr($type,0,1));
            $img = URL.'/img/logo/logo_dataverse.png';
            if ($type=='i')
                {
                    $img = '<img src="'.$img.'" $par>';
                }
            return $img;
        }

    function index($d1,$d2,$d3,$d4)
        {
            $sx = 'DATAVERSE';
            $sx .= troca($this->logo('IMG'),'$par','height="100px;" align="right"');

            switch($d1)
                {
                    case 'server':
                        $sx .= form_server();
                        break;
                    default:
                        $sx .= $this->menu();
                }

            $sx = bs(bsc($sx));
            return $sx;
        }

    function form_server()
        {
            $sx = form_open();
            $sx .= form_input('server',$server,'class'=>)
        }

    function server()
        {
            if (isset($_SESSION['dataverse_server']))
                {
                    return $_SESSION['datavese_server'];
                } else {
                    return 'none';
                }
        }

    function menu()
        {
            $server = $this->server();
            $menu = array();
            $menu[PATH.'/dados/dataverse/server'] = lang('dataverse.server').': '.$server;

            return menu($menu);
        }
}
