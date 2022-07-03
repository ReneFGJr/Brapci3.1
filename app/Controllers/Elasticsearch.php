<?php

namespace App\Controllers;

use App\Controllers\BaseController;

$this->session = \Config\Services::session();
$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'Elasticsearch');

class Elasticsearch extends BaseController
{
    public function index($act = '', $id = '')
    {
        $data['page_title'] = 'Brapci-Tools-ElasticSearch';
        $data['bg'] = 'bg-tools';
        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Benancib/Headers/navbar', $data);

        $act = trim($act);
        
        switch ($act) {
            case 'register':
                $APIRegister = new \App\Models\ElasticSearch\Register();
                $sx .= $APIRegister->register($id);

            case 'status':
                $API = new \App\Models\ElasticSearch\API();
                $dt = $API->status();
                $sx .= $API->showList($dt);
            break;
            default:
                $data['logo'] = view('Tools/Svg/logo_elasticsearch');
                $sx .= view('Tools/WelcomeElasticSearch', $data);
                $sx .= $this->menu();
                break;
        }
        $sx .= h($act);
        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }    

    private function  menu()
        {
            $menu[URL.'/Elasticsearch/status'] = lang('elastic.status');
            $menu[URL.'/Elasticsearch/register/1'] = lang('elastic.register_test');
            $sx = menu($menu);
            return $sx;
        }
}
