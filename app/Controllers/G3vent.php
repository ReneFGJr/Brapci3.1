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

class G3event extends BaseController
{
    public function index($d1 = '', $d2 = '', $d3 ='', $d4 ='', $d5 = '')
    {
        $data = [];
        $sx = '';
        $sx .= view('DCI/Headers/header', $data);
        $sx .= view('DCI/Headers/navbar', $data);

        $G3vent = new \App\Models\Gev3nt\Inscritos();
        switch($d1)
            {
                default:
                    $sx .= $G3vent->lista_eventos();
            }
        $sx .= view('DCI/Headers/footer', $data);
        return $sx;
    }
}