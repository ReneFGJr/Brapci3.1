<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("COLLECTION", '/patente');
define("MODULE", 'patente');
define("PREFIX", '');
define("LIBRARY", '1000');

class Patente extends BaseController
{
    public function index($act = '', $d1 = '', $d2 = '', $d3 = '', $d4 = '')
    {
        $data['page_title'] = 'Brapci - Patentes';
        $data['bg'] = 'bg-patente d-print-none';

        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Ai/Header/navbar', $data);
        $sx .= view('Brapci/Pages/carrossel', $data);


        switch ($act) {
            case 'v':
                $sx .= $this->v($d1);
                break;
            case 'harvesting':
                $sx .= $this->harvesting($d1);
                break;

            case 'proccess':
                $sx .= $this->proccess();
                break;

            default:
                $menu = array();
                $menu['#'.lang('patent.RPI')] = '';
                $menu[PATH . COLLECTION . '/harvesting'] = lang('patent.harvesting');
                $menu[PATH . COLLECTION . '/proccess'] = lang('patent.proccess');
                $sx .= bs(bsc(menu($menu), 12));
                break;
        }
        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }

    function v($id)
        {
            $RPIIssue = new \App\Models\Patent\RPIIssue;
            $RPIDespacho = new \App\Models\Patent\RPIDespacho;
            $data = array();
            $data['despacho'] = $RPIDespacho->show($id);
            $sx = view('Patente/View', $data);

            return $sx;
        }

    function proccess()
    {
        $RPI_import = new \App\Models\Patent\RPIImport();
        $sx = $RPI_import->proccess(-1);
        return $sx;
    }

    function harvesting($d1)
        {
            $sx = '';
            if ($d1 == '') { $d1 = 2668; }
            $Patent = new \App\Models\Patent\Index();
            $sx .= $Patent->index('harvesting',$d1);

            return $sx;
        }
}
