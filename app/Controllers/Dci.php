<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("COLLECTION", '/dci');
define("PREFIX", '');

class Dci extends BaseController
{
    public function index($d1 = '', $d2 = '', $d3 = '', $d4 = '')
    {
        $sx = '';

        $data['page_title'] = 'DCI - UFRGS';
        $data['bg'] = 'bg-brapcilivros';
        $sx = '';
        $sx .= view('DCI/Headers/header', $data);
        $sx .= view('DCI/Headers/navbar', $data);

        $DCI = new \App\Models\Dci\Index();
        $sx .= $DCI->index($d1,$d2,$d3,$d4);
        $sx .= view('DCI/Headers/footer', $data);
        return $sx;
    }
}