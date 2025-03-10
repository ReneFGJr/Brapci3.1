<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'crawler');

class Crawler extends BaseController
{
    public function index($act = '', $subact = '', $id = '', $id2='')
    {
        $Crawler = new \App\Models\Crawler\Index();
        $data['page_title'] = 'Brapci Crawlers';
        $data['bg'] = 'bg-ai';

        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Ai/Header/navbar', $data);
        switch ($act) {

            case 'webcrawler':
                $Crawler = new \App\Models\Crawler\Webcrawler\Index();
                $sx .= $Crawler->index($subact, $id, $id2);
                break;

            case 'viewid':
            $sx .= $Crawler->viewid($subact);
            break;

           default:
                $sx .= $Crawler->index($act,$subact,$id,$id2);
                break;
        }

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }
}