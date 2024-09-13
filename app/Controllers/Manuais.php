<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'dci');

class Manuais extends BaseController
{
    public function index($d1 = '', $d2 = '', $d3 ='', $d4 ='', $d5 = '')
    {
        $sx = '';

        $data['page_title'] = 'Manuais - UFRGS';
        $data['bg'] = 'bg-brapcilivros';
        $sx = '';
        $sx .= view('DCI/Headers/header', $data);
        $sx .= view('DCI/Headers/navbar', $data);

        $Manuais = new \App\Models\Manuais\Index();
        $sx .= $Manuais->index($d1,$d2,$d3,$d4,$d5);
        return $sx;
    }
}