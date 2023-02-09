<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/* SESSION */
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
        $Socials = new \App\Models\Socials();
        if (!$Socials->getAccess("#ADM")) {
            $sx = metarefresh(PATH);
            return $sx;
        }
        $data['page_title'] = 'Brapci-Tools-ElasticSearch';
        $data['bg'] = 'bg-tools';
        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Brapci/Headers/navbar', $data);

        $act = trim($act);

        switch ($act) {
            case 'database':
                $APIRegister = new \App\Models\ElasticSearch\Register();
                $sx .= bs(bsc($APIRegister->resume()));
                break;

            case 'register':
                $RDF = new \App\Models\Rdf\RDF();
                $RDF->c($id);
                $APIRegister = new \App\Models\ElasticSearch\Register();
                $sx .= $APIRegister->register($id,'test');
                break;

            case 'list':
                $API = new \App\Models\ElasticSearch\API();
                $rsp = $API->list_index();
                $idx = array();
                foreach($rsp as $id=>$index)
                    {
                        $idx[$index['index']] = $index;
                    }

                ksort($idx);
                $sx .= '<table class="table" width="100%">';
                $sx .= '<tr>';
                $sx .= '<th>Index</th>';
                $sx .= '<th width="10%">Status</th>';
                $sx .= '<th width="10%">Nº Docs</th>';
                $sx .= '<th width="10%">Docs Deleted</th>';
                $sx .= '<th width="10%">Size</th>';
                $sx .= '<th width="10%">Action</th>';
                $sx .= '</tr>';
                foreach($idx as $index=>$data)
                    {
                        $trash = '<a href="'.PATH.'/elasticsearch/delete/'.$index.'" style="color: red">'.bsicone('trash').'</a>';
                        $sx .= '<tr>';
                        $sx .= '<td>'.$index.'</td>';
                        $sx .= '<td>' . $data['status'] . '</td>';
                        $sx .= '<td class="text-end">'. number_format($data['docs.count'],0,',','.').'</td>';
                        $sx .= '<td class="text-end">' . number_format($data['docs.deleted'],0,',','.') . '</td>';
                        $sx .= '<td class="text-end">' . $data['store.size'] . '</td>';
                        $sx .= '<td>'.$trash.'</td>';
                        $sx .= '</tr>';
                    }
                $sx .= '</table>';
                $sx = bs(bsc($sx,12));
                break;

            case 'delete':
                $API = new \App\Models\ElasticSearch\API();
                $rst = $API->delete_index($id);
                if (isset($rst['acknowledged']))
                    {
                        if ($rst['acknowledged'] == '1')
                            {
                                $sa = '';
                                $sa .= bsmessage('Index '.$id.' has deleted',1);
                                $sa .= metarefresh(PATH.'/elasticsearch/list',1);
                                $sx .= bs(bsc($sa,12));
                            } else {
                                pre($rst);
                            }
                    } else {
                        pre($rst);
                    }
                break;

            case 'info':
                $API = new \App\Models\ElasticSearch\API();
                $sx .= $API->info();
                break;

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
        $id = 200098;
        $menu['#Database'] = '';
        $menu[PATH . '/elasticsearch/database'] = lang('elastic.database');

        $menu['#Tools'] = '';
        $menu[PATH . '/elasticsearch/status'] = lang('elastic.status');
        $menu[PATH . '/elasticsearch/register/'.$id] = lang('elastic.register_test');
        $menu[PATH . '/elasticsearch/search/'] = lang('elastic.search');
        $menu[PATH . '/elasticsearch/list/'] = lang('elastic.list_index');
        $sx = menu($menu);
        return $sx;
    }
}