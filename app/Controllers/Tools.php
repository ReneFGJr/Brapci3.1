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
    public function index($act = '', $subact = '', $id = '', $id2='',$id3='',$id4='',$id5='')
    {
        $Tools = new \App\Models\Tools\Index();
        $data['page_title'] = 'Brapci Bibliometric Tools';
        $data['bg'] = 'bg-tools';

        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Brapci/Headers/navbar', $data);
        switch ($act) {
            case 'social':
                $Socials = new \App\Models\Socials();
                $sx .= bs(bsc($Socials->index($subact, $id), 12));
                break;

           case 'project':
            $Projects = new \App\Models\Tools\Projects();
            $sx .= $Projects->index($subact,$id,$id2, $id3, $id4, $id5);
            break;

           default:
                $sx .= $Tools->index($act,$subact,$id,$id2,$id3,$id4, $id5);
                break;
        }

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }
}