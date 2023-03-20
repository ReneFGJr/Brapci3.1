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

        switch($d1)
            {
                default:
                    $menu[PATH.'/dci/docentes/'] = 'Docentes';
                    $menu[PATH . '/dci/cursos/'] = 'Cursos';
                    $menu[PATH . '/dci/disciplinas/'] = 'Docentes';
                    $menu[PATH . '/dci/encargos/'] = 'Encargos';
                    $menu[PATH . '/dci/semestre/'] = 'Semestre';

                    $sx  .= menu($menu);
                    $sx = bs(bsc($sx));
                    break;
            }

        $API = new \App\Models\Dci\Index();
        $sx .= $API->index($d1, $d2, $d3, $d4);
        return $sx;
    }
}