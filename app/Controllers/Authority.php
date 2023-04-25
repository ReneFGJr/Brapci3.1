<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'autoridade');

class Authority extends BaseController
{
    public function index($act = '', $subact = '', $id = '', $id2 = '')
    {
        $Authority = new \App\Models\Authority\Index();
        $RDF = new \App\Models\Rdf\RDF();

        $data['page_title'] = 'Brapci-Autoridades';
        $data['GOOGLEID'] = 'UA-12713129-1';
        $data['bg'] = 'bg-authority';
        $data['bg_color'] = '#0000ff';
        $menu = array();
        $menu[PATH . '/'] = lang('brapci.journals');
        $menu[PATH . '/books'] = lang('brapci.books');
        $menu[PATH . '/benancib'] = lang('brapci.benancib');
        $menu[PATH . '/autoridade'] = lang('brapci.authorities');

        $data['menu'] = $menu;
        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Brapci/Headers/navbar', $data);
        //$sx .= view('Brapci/Headers/menu_authorities');

        switch ($act) {

            case 'v':
                $V = new \App\Models\Base\V();
                $sx .= $V->v($subact);
                break;
            case 'list':
                $sx .= $Authority->index($act, $subact, $id, $id2);
                break;
            default:
                $sx .= '';
                $Genere = new \App\Models\Authority\Genere();
                $data['genere'] = $Genere->summary();
                $data['search'] = view('Authority/Search');
                $data['search_result'] = '';

                $search = get("q");
                if ($search != '')
                    {
                        $Elastic = new \App\Models\ElasticSearch\Search();
                        $rst = $Elastic->search($search, 'autoridade');
                        $txt = '';
                        foreach($rst['works'] as $id=>$line)
                            {
                                $txt .= '<a href="'.PATH.'/v/'.$line['id'].'">';
                                $txt .= $RDF->c($line['id']);
                                $txt .= '</a>';
                                $txt .= '<hr>';
                            }
                        $data['search_result'] = $txt;
                    }

                $sx .= view('Authority/World',$data);
                break;
        }

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }
}