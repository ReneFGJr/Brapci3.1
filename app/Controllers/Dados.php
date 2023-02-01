<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'dados');

class Dados extends BaseController
{
    public function index($act = '', $subact = '', $id = '', $id2 = '')
    {
        $ResearchData = new \App\Models\ResearchData\Index();
        $menu = array();
        $menu[PATH.'/dados/dataverse'] = lang('brapci.dataverse');
        $data['menu'] = $menu;

        $data['page_title'] = 'Brapci Dados de Pesquisa';
        $data['bg'] = 'bg-ai';

        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Brapci/Headers/navbar', $data);
        switch ($act) {
            case 'tombstone':
                $DOI = new \App\Models\DOI\Index();
                $sx .= $DOI->tombstone($subact, $id, $id2);
                break;
            case 'dataverse':
                $Dataverse = new \App\Models\Dataverse\Index();
                $sx .= $Dataverse->index($subact,$id,$id2);
                break;
            default:
                $sx .= $ResearchData->index($act, $subact, $id, $id2);
                break;
        }

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }
}