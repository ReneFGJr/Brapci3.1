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
                $sx .= view('Tools/WelcomeElasticSearch', $data);            
                $sx .= $this->search();
                break;

            default:
                $sx .= h($act);
                $data['logo'] = view('Tools/Svg/logo_elasticsearch');
                $sx .= bs(bsc(view('Tools/WelcomeElasticSearch', $data),12));
                $sx .= bs(bsc($this->menu(),12));
                break;
        }        
        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }  

    function show_works($dt)
        {
            $RDF = new \App\Models\Rdf\RDF();
            $sx = '';
            if (!isset($dt['total'])) { return ''; }

            $sx .= 'Total '.$dt['total'];
            $sx .= ', mostrando '.$dt['start'].'/'.$dt['offset'];

            for ($r=0;$r < count($dt['works']);$r++)
                {
                    $line = $dt['works'][$r];
                    $sx .= bsc($RDF->c($line['id']).' <sup>(Score: '.number_format($line['score'],3,'.',',').')</sup>');
                }
            $sx = bs($sx);
            return $sx;
        }

    function search()
        {            
            $sx = '';
            $sx .= form_open();
            $sx .= 'Termo de busca';
            $sx .= form_input(array('name'=>'search','class'=>"form-control"));
            $sx .= form_submit(array('name'=>'action','value'=>'busca'));
            $sx .= form_close();
            $sx = bs(bsc($sx,12));

            if (get("search") != '')
                {
                    $q = get("search");
                    $Search = new \App\Models\ElasticSearch\Search();
                    $sx .= $this->show_works($Search->search($q));
                }
            return $sx;

        } 

    private function  menu()
        {
            $menu[URL.'/elasticsearch/status'] = lang('elastic.status');
            $menu[URL.'/elasticsearch/register/1'] = lang('elastic.register_test');
            $menu[URL.'/elasticsearch/search/'] = lang('elastic.search');
            $sx = menu($menu);
            return $sx;
        }
}
