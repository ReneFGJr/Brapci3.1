<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/* SESSION */
$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("LIBRARY", '1000');
define("COLLECTION", 'pq/');

class Pq extends BaseController
{
    public function index($d1 = '', $d2 = '', $d3 = '', $d4 = '')
    {
        $PQ = new \App\Models\PQ\Index();
        $data['page_title'] = 'Brapci - Pesquisadores Produtividade PQ CNPq';
        $data['bg'] = 'bg-pq';
        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Brapci/Headers/navbar', $data);
        $sa = '<a href="' . PATH . '/pq">' . view('Pq/logo', $data) . '</a>';
        $sb = $PQ->index($d1, $d2, $d3, $d4);
        $sx .= bs(bsc($sa, 2) . bsc($sb, 10));
        $sx .= view('Brapci/Headers/footer', $data);
        $sx .= '<div style="height: 80px; wdith: 100%;">X</div>';
        return $sx;
    }
}