<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'tools');

class Tools extends BaseController
{
    public function index($act = '', $subact = '', $id = '', $id2='')
    {
        $Tools = new \App\Models\Tools\Index();
        $data['page_title'] = 'Brapci Bibliometric Tools';
        $data['bg'] = 'bg-ai';

        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Ai/Header/navbar', $data);
        switch ($act) {

           default:
                $sx .= $Tools->index($act,$subact,$id,$id2);
                break;
        }

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }
}