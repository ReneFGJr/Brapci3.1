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

class Oai extends BaseController
{
    private function cab()
        {
            $menu = [];
            $data['menu'] = $menu;
            $data['page_title'] = 'OAI-PMH | Brapci';
            $data['bg'] = 'bg-ai';

            $sx = '';
            $sx .= view('Brapci/Headers/header', $data);
            return $sx;
        }
    public function index($act = '', $subact = '', $id = '', $id2='',$id3='',$id4='',$id5='')
    {
        $verb = get("verb");
        switch($verb)
            {
                default:
                    $sx = $this->cab();
                    $sx .= view('oai/ide');
                    break;
            }
        return $sx;
    }
}