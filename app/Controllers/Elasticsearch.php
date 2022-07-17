<?php

namespace App\Controllers;

use App\Controllers\BaseController;

$this->session = \Config\Services::session();
$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
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
        $sx .= view('Brapci/Headers/navbar', $data);

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

            case 'search':
                $data['logo'] = view('Tools/Svg/logo_elasticsearch');
                $sx .= view('Tools/Elasticsearch/WelcomeElasticSearch', $data);
                $sx .= view('Tools/Elasticsearch/Form', $data);
                $sx .= $this->search();
                break;

            default:
                $sx .= h($act);
                $data['logo'] = view('Tools/Svg/logo_elasticsearch');
                $sx .= bs(bsc(view('Tools/Elasticsearch/WelcomeElasticSearch', $data), 12));
                $sx .= bs(bsc($this->menu(), 12));
                break;
        }
        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }

    function search()
    {
        $sx = '';
        if (get("search") != '') {
            $q = get("search");
            $Search = new \App\Models\ElasticSearch\Search();
            $SearchElastic = new \App\Models\ElasticSearch\Index();
            $sx .= $SearchElastic->show_works($Search->search($q));
        }
        return $sx;
    }

    private function  menu()
    {
        $menu[PATH . 'elasticsearch/status'] = lang('elastic.status');
        $menu[PATH . 'elasticsearch/register/1'] = lang('elastic.register_test');
        $menu[PATH . 'elasticsearch/search/'] = lang('elastic.search');
        $sx = menu($menu);
        return $sx;
    }
}