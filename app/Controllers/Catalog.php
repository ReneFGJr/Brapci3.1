<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("COLLECTION", '/catalog');
define("PREFIX", '');
define("MODULE", 'catalog');
define("LIBRARY", '1000');

class Catalog extends BaseController
{
    public function index($act = '',$d1='',$d2='',$d3='',$d4='')
    {
        $sx = '';
        $data['page_title'] = 'Brapci Dados de Pesquisa';
        $data['bg'] = 'bg-authority';

        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Brapci/Headers/navbar', $data);

        $sx .= $this->services($act,$d1,$d2,$d3,$d4);

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }

    function services($act, $d1, $d2, $d3, $d4)
        {
            $menu = array();
            $menu['#brapci.serviceplace'] = "#";
            $menu['/catalog'] = msg('brapci.catalog');
            $menu['/books'] = msg('brapci.books');
            $menu['/tools'] = msg('brapci.bibliometric_tools');
            $menu['/autoridades'] = msg('brapci.authority');

            return bs(bsc(menu($menu),12));
        }
}