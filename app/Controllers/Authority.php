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
        $ResearchData = new \App\Models\Authority\Index();
        $data['page_title'] = '[Authority] Brapci Dados de Pesquisa';
        $data['bg'] = 'bg-authority';

        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Ai/Header/navbar', $data);
        switch ($act) {

            default:
                $sx .= $ResearchData->index($act, $subact, $id, $id2);
                break;
        }

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }
}