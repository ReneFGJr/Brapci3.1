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
    public function index($act = '')
    {
        $sx = '';
        $data['page_title'] = 'Brapci Dados de Pesquisa';
        $data['bg'] = 'bg-ai';

        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Ai/Header/navbar', $data);

        $sx .= $this->services();

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }

    function services()
        {
            $menu = array();
            $menu['#brapci.public'] = "#";
            $menu['/catalog'] = msg('brapci.catalog');
            $menu['/books'] = msg('brapci.books');

            return bs(bsc(menu($menu),12));
        }
}