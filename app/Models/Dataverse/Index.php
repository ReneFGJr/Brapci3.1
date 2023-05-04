<?php

namespace App\Models\Dataverse;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'dataverse';
    protected $table            = 'dataverse';
    protected $primaryKey       = 'id_dvn';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_dvn', 'dvn_title', 'dvn_url',
        'dvn_id', 'dvn_type', 'dvn_father',
        'created_at', 'dvn_collection'
        ];

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

    function register($title,$id,$type,$father,$collection)
        {
            $data['dvn_title'] = $title;
            $data['dvn_url'] = $title;
            $data['dvn_id'] = $id;
            $data['dvn_type'] = $type;
            $data['dvn_father'] = $father;
            $data['dvn_collection'] = $collection;

            $dt = $this
                ->where('dvn_collection', $collection)
                ->where('dvn_id', $id)
                ->first();

            pre($dt);
        }

    function logo($type = '')
    {
        $type = lowercase(substr($type, 0, 1));
        $img = URL . '/img/logo/logo_dataverse.png';
        if ($type == 'i') {
            $img = '<img src="' . $img . '" $par>';
        }
        return $img;
    }

    function index($d1, $d2, $d3, $d4)
    {
        $sx = 'DATAVERSE';
        $sx .= troca($this->logo('IMG'), '$par', 'height="100px;" align="right"');

        switch ($d1) {
            case 'header':
                $Header = new \App\Models\Dataverse\Custom\Header();
                $sx .= $Header->index($d2, $d3, $d3);
                break;
            case 'translate':
                $Translate = new \App\Models\Dataverse\Translate();
                $sx .= $Translate->index($d2, $d3, $d3);
                break;
            case 'migration':
                $Migration = new \App\Models\Dataverse\Migration();
                $sx .= $Migration->index($d2,$d3,$d3);
                break;
            case 'logo':
                $Logo = new \App\Models\Dataverse\Custom\Logo();
                $sx .= $Logo->index($d2,$d3,$d4);
                break;

            case 'preinstall':
                $PreInstall = new \App\Models\Dataverse\Install\PreInstall();
                $sx .= $PreInstall->index($d2, $d3, $d4);
                break;
            case 'tsv':
                $Application = new \App\Models\Dataverse\ApplicationPerfil();
                $sx .= $Application->index($d2,$d3,$d4);
                break;
                break;
            case 'licences':
                $Licences = new \App\Models\Dataverse\Licences();
                $sx .= $Licences->index($d2,$d3,$d4);
                break;
            case 'indicators':
                $sx .= $this->indicators();
                break;
            case 'server':
                $sx .= h('Configurações de registros', 2);
                $sx .= $this->form_server();
                break;
            default:
                $sx .= $this->menu();
        }

        $sx = bs(bsc($sx));
        return $sx;
    }

    function menu()
    {
        $server = $this->server();
        $menu = array();
        $menu[PATH . '/dados/dataverse/server'] = lang('dataverse.server') . ': ' . $server;

        $menu[PATH . '/dados/dataverse/indicators'] = lang('dataverse.indicators');

        $menu['#CONFIGURATIONS'] = '';
        $menu[PATH . '/dados/dataverse/licences'] = lang('dataverse.licences');
        $menu[PATH . '/dados/dataverse/tsv'] = lang('dataverse.application_perfil');

        $menu['#CUSTOMIZE'] = '';
        $menu[PATH . '/dados/dataverse/logo'] = lang('dataverse.customize_logo');
        $menu[PATH . '/dados/dataverse/header'] = lang('dataverse.customize_header');
        $menu[PATH . '/dados/dataverse/footer'] = lang('dataverse.customize_footer');
        $menu[PATH . '/dados/dataverse/homepage'] = lang('dataverse.customize_homepage');
        $menu[PATH . '/dados/dataverse/css'] = lang('dataverse.customize_style');
        $menu[PATH . '/dados/dataverse/migration'] = lang('dataverse.migration');
        $menu[PATH . '/dados/dataverse/translate'] = lang('dataverse.translate');

        $menu['#CHECKLIST'] = '';
        $menu[PATH . '/dados/dataverse/preinstall'] = lang('dataverse.pre_install');

        $menu['#CHECKLIST'] = '';
        $menu[PATH . '/dados/dataverse/checklist_instalation'] = lang('dataverse.checklist_instalation');

        return menu($menu);
    }

    function indicators()
        {
            $server = $this->server();
            $sx = '';

            if ($server == '')
                {
                    $sx .= bsmessage(lang('dataverse.url_server_not_defined'),3);
                    return $sx;
                }
            $sx .= h($server);

            $Metrics = new \App\Models\Dataverse\ApiDOC\Metric();
            $sx .= $Metrics->all();
            return $sx;
        }

    function form_server()
    {
        $server = $this->server(get("server"));
        $token = $this->token(get("token"));

        if (get("action") != '')
            {
                return metarefresh(PATH.'/dados/dataverse/');
                exit;
            }

        $sx = bsc(form_open(),12);

        $sa = form_label(lang('brapci.url_input'));
        $sa .= form_input(array('name' => 'server', 'value' => $server, 'class' => 'form-control-hs full'));

        $sb = form_label(lang('brapci.token_input'));
        $sb .= form_input(array('name' => 'token', 'value' => $token, 'class' => 'form-control-hs full'));

        $sx .= bsc($sa,6).bsc($sb,6);


        $sf = form_submit(array('name'=>'action', 'value'=>lang('brapci.save'),'class'=>'mt-3'));
        $sf .= form_close();
        $sf = bsc($sf,12);

        $sx = bs($sx.$sf);
        return $sx;
    }

    function getServer()
        {
            return $this->server();
        }

    function server($url = '')
    {
        if ($url != '') {
            $_SESSION['dataverse_server'] = $url;
            return $url;
        } else {
            if (isset($_SESSION['dataverse_server']))
            {
                return $_SESSION['dataverse_server'];
            } else {
                return '';
            }
        }
    }

    function getToken()
    {
        return $this->token();
    }

    function token($url = '')
    {
        if ($url != '') {
            $_SESSION['dataverse_token'] = $url;
            return $url;
        } else {
            if (isset($_SESSION['dataverse_token'])) {
                return $_SESSION['dataverse_token'];
            } else {
                return '';
            }
        }
    }


}
