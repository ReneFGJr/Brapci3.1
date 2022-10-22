<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("COLLECTION", '/parecer');
define("MODULE", 'parecer');
define("PREFIX", '');
define("LIBRARY", '1000');

class Parecer extends BaseController
{
    public function index($act = '',$d1='',$d2='',$d3='',$d4='')
    {
        $data['page_title'] = 'Brapci - Pareceres';
        $data['bg'] = 'bg-ai d-print-none';

        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Ai/Header/navbar', $data);


        switch ($act) {
            case 'checklist':
                $Checklist = new \App\Models\ScientificCommunication\Check();
                $sx .= $Checklist->index($d1, $d2, $d3, $d4);
                break;
            case 'opinion':
                $PeerReview = new \App\Models\ScientificCommunication\PeerReview();
                $sx .= $PeerReview->index($d1,$d2,$d3,$d4);
                break;
            case 'reference':
                $PeerReview = new \App\Models\ScientificCommunication\Reference();
                $sx .= $PeerReview->index($d1, $d2, $d3, $d4);
                break;

            default:
                $menu = array();
                $menu[PATH . COLLECTION . '/opinion'] = lang('peer.scientific_opinion');
                $menu[PATH . COLLECTION . '/reference'] = lang('peer.reference_analysis');
                $menu[PATH . COLLECTION . '/checklist'] = lang('peer.checklist');
                $sx .= bs(bsc(menu($menu),12));
                break;
        }
        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }
}
