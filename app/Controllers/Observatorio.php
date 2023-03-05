<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("COLLECTION", '/observatorio');
define("MODULE", 'observatorio');
define("PREFIX", '');
define("LIBRARY", '0000');

class Observatorio extends BaseController
{
    public function index($act = '',$d1='',$d2='',$d3='',$d4='')
    {
        $data['page_title'] = 'Brapci - Observatório';
        $data['bg'] = 'bg-ai d-print-none';

        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Ai/Header/navbar', $data);
        $sx .= view('Brapci/Headers/space', $data);


        switch ($act) {
            case 'project':

            $sx .= h('Observatório: '.$d1.' '.$d2);

            $ScrapingLattes = new \App\Models\AI\NLP\ScrapingLattes();
            $sx .= $ScrapingLattes->search();

            $sx = bs(bsc($sx, 12));
            break;

            default:
                $menu = array();
                $menu[PATH . COLLECTION . '/project/oc'] = lang('observatorio.open_science');
                $sx .= bs(bsc(menu($menu),12));
                break;
        }
        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }
}
